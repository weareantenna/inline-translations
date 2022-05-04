<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use League\Flysystem\Filesystem;

use function array_key_exists;
use function array_merge;
use function assert;
use function basename;
use function dirname;
use function is_array;
use function is_string;
use function pathinfo;
use function str_replace;

use const PATHINFO_FILENAME;

class TranslationFetcher
{
    private Filesystem $filesystem;
    private string $basePath;

    public function __construct(Filesystem $filesystem, string $basePath)
    {
        $this->filesystem = $filesystem;
        $this->basePath   = $basePath;
    }

    /** @return array<string, array<string>> */
    public function fetchAll(): array
    {
        $rootFolderAndFiles = $this->filesystem->listContents('');

        $translations = [];
        /** @var array<string, string> $rootFolderOrFile */
        foreach ($rootFolderAndFiles as $rootFolderOrFile) {
            if ($rootFolderOrFile['type'] !== 'dir') {
                continue;
            }

            $language = basename($rootFolderOrFile['path']);

            if ($language === 'vendor') {
                continue;
            }

            if (! is_string($language)) {
                continue;
            }

            $translations[$language] = $this->fetchByLanguage($language);
        }

        return $translations;
    }

    /** @return array<int|string,array<string, string>> */
    public function fetchAllGroupedByKeys(): array
    {
        $translationsByLanguage = $this->fetchAll();

        $result = [];
        foreach ($translationsByLanguage as $language => $translations) {
            foreach ($translations as $key => $value) {
                if (! array_key_exists($key, $result)) {
                    $result[$key] = [];
                }

                $result[$key][$language] = $value;
            }
        }

        return $result;
    }

    /** @return string[] */
    public function fetchByLanguage(string $language): array
    {
        return array_merge(
            $this->parseLanguageFile($language),
            $this->getVendorTranslationsByLanguage($language)
        );
    }

    /** @return string[] */
    private function parseLanguageFile(string $language, string $basePath = '', string $prefix = ''): array
    {
        $languageFiles = $this->filesystem->listContents($basePath . $language);

        $translations = [];

        /** @var array<string, string> $languageFile */
        foreach ($languageFiles as $languageFile) {
            if ($languageFile['type'] === 'dir') {
                continue;
            }

            $translationContent = require $this->basePath . $languageFile['path'];

            $translations = array_merge(
                $translations,
                $this->flattenTranslationKeys(
                    $translationContent,
                    $prefix . pathinfo($languageFile['path'], PATHINFO_FILENAME)
                )
            );
        }

        return $translations;
    }

    /** @return string[] */
    private function getVendorTranslationsByLanguage(string $language): array
    {
        $vendorFolders = $this->filesystem->listContents('vendor');

        $translations = [];

        /** @var array<string, string> $vendorFolder */
        foreach ($vendorFolders as $vendorFolder) {
            $vendorTranslations = $this->filesystem->listContents($vendorFolder['path']);

            /** @var array{dirname: string, path: string} $vendorTranslation */
            foreach ($vendorTranslations as $vendorTranslation) {
                if (basename($vendorTranslation['path']) !== $language) {
                    continue;
                }

                $package = str_replace('vendor/', '', dirname($vendorTranslation['path']));
                assert(is_string($package));
                $translations = array_merge(
                    $translations,
                    $this->parseLanguageFile($language, dirname($vendorTranslation['path']) . '/', $package . '::')
                );
            }
        }

        return $translations;
    }

    /**
     * @param array<string, mixed> $translationArray
     * @param string[]             $flattenedArray
     *
     * @return string[]
     */
    private function flattenTranslationKeys(array $translationArray, string $fileName, array $flattenedArray = []): array
    {
        foreach ($translationArray as $key => $translation) {
            if (is_array($translation)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    //@phpstan-ignore-next-line
                    $this->flattenTranslationKeys($translation, $fileName . '.' . $key, $flattenedArray)
                );
            } else {
                $flattenedArray[$fileName . '.' . $key] = $translation;
            }
        }

        /** @var string[] $flattenedArray */
        return $flattenedArray;
    }
}
