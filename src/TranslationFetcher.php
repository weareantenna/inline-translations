<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use League\Flysystem\Filesystem;
use function array_merge;
use function assert;
use function is_array;
use function is_string;
use function str_replace;

class TranslationFetcher
{
    private Filesystem $filesystem;
    private string $basePath;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->basePath   = $this->filesystem->getAdapter()->getPathPrefix(); //@phpstan-ignore-line
    }

    /** @return array<string, array<string>> */
    public function fetchAll() : array
    {
        $rootFolderAndFiles = $this->filesystem->listContents();

        $translations = [];
        foreach ($rootFolderAndFiles as $rootFolderOrFile) {
            if ($rootFolderOrFile['type'] !== 'dir') {
                continue;
            }

            $language = $rootFolderOrFile['basename'];

            if ($language === 'vendor') {
                continue;
            }

            $translations[(string) $language] = $this->fetchByLanguage($language);
        }

        return $translations;
    }

    /** TODO: we should also return not translated values or handle this in the frontend so that new translations can be done */
    public function fetchAllGroupedByKeys(): array
    {
        $translationsByLanguage = $this->fetchAll();

        $result = [];
        foreach ($translationsByLanguage as $language => $translations) {
            foreach ($translations as $key => $value) {
                if (!array_key_exists($key, $result)) {
                    $result[$key] = [];
                }

                $result[$key][$language] = $value;
            }
        }

        return $result;
    }

    /** @return string[] */
    public function fetchByLanguage(string $language, string $basePath = '', string $prefix = '') : array
    {
        return array_merge(
            $this->parseLanguageFile($language),
            $this->getVendorTranslationsByLanguage($language)
        );
    }

    /** @return string[] */
    private function parseLanguageFile(string $language, string $basePath = '', string $prefix = '') : array
    {
        $languageFiles = $this->filesystem->listContents($basePath . $language);

        $translations = [];
        foreach ($languageFiles as $languageFile) {
            if ($languageFile['type'] === 'dir') {
                continue;
            }

            $translationContent = require_once $this->basePath . $languageFile['path'];

            $translations = array_merge(
                $translations,
                $this->flattenTranslationKeys(
                    $translationContent,
                    $prefix . $languageFile['filename']
                )
            );
        }

        return $translations;
    }

    /** @return string[] */
    private function getVendorTranslationsByLanguage(string $language) : array
    {
        $vendorFolders = $this->filesystem->listContents('vendor');

        $translations = [];
        foreach ($vendorFolders as $vendorFolder) {
            $vendorTranslations = $this->filesystem->listContents($vendorFolder['path']);

            foreach ($vendorTranslations as $vendorTranslation) {
                if ($vendorTranslation['basename'] !== $language) {
                    continue;
                }

                $package = str_replace('vendor/', '', $vendorTranslation['dirname']);
                assert(is_string($package));
                $translations = array_merge(
                    $translations,
                    $this->parseLanguageFile($language, $vendorTranslation['dirname'] . '/' . $package . '::')
                );
            }
        }

        return $translations;
    }

    /**
     * @param array<string|array> $translationArray
     * @param string[]            $flattenedArray
     *
     * @return string[]
     */
    private function flattenTranslationKeys(array $translationArray, string $fileName, array $flattenedArray = []) : array
    {
        foreach ($translationArray as $key => $translation) {
            if (is_array($translation)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->flattenTranslationKeys($translation, $fileName . '.' . $key, $flattenedArray)
                );
            } else {
                $flattenedArray[$fileName . '.' . $key] = $translation;
            }
        }

        return $flattenedArray;
    }
}
