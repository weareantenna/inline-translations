<?php

namespace Antenna\InlineTranslations;

use League\Flysystem\Filesystem;

class TranslationFetcher
{
    private Filesystem $filesystem;
    private string $basePath;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->basePath = $this->filesystem->getAdapter()->getPathPrefix();
    }

    /**
     * TODO: include vendor translations
     */
    public function fetchAll(): array
    {
        $rootFolderAndFiles = $this->filesystem->listContents();

        $translations = [];
        foreach ($rootFolderAndFiles as $rootFolderOrFile) {
            if ($rootFolderOrFile['type'] === 'dir') {
                $language = $rootFolderOrFile['basename'];

                if ($language !== 'vendor') {
                    $translations[$language] = $this->fetchByLanguage($language);
                }
            }
        }

        return $translations;
    }

    public function fetchByLanguage(string $language, string $basePath = '', string $prefix = ''): array
    {
        return array_merge(
            $this->parseLanguageFile($language),
            $this->getVendorTranslationsByLanguage($language)
        );
    }

    private function parseLanguageFile(string $language, string $basePath = '', string $prefix = ''): array
    {
        $languageFiles = $this->filesystem->listContents($basePath . $language);

        $translations = [];
        foreach($languageFiles as $languageFile) {
            $translationContent = require_once($this->basePath . $languageFile['path']);

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

    private function getVendorTranslationsByLanguage(string $language): array
    {
        $vendorFolders = $this->filesystem->listContents('vendor');

        $translations = [];
        foreach ($vendorFolders as $vendorFolder) {
            $vendorTranslations = $this->filesystem->listContents($vendorFolder['path']);

            foreach ($vendorTranslations as $vendorTranslation) {
                if($vendorTranslation['basename'] !== $language) {
                    continue;
                }

                $package = str_replace('vendor/', '', $vendorTranslation['dirname']);
                $translations = array_merge(
                    $translations,
                    $this->parseLanguageFile($language, $vendorTranslation['dirname'] .'/', $package . '::')
                );
            }
        }

        return $translations;
    }

    private function flattenTranslationKeys(array $translationArray, string $fileName, array $flattenedArray = []) {
        foreach($translationArray as $key => $translation) {
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
