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

    public function fetchByLanguage(string $language): array
    {
        $languageFiles = $this->filesystem->listContents($language);

        $translations = [];
        foreach($languageFiles as $languageFile) {
            $translations[$languageFile['filename']] = require_once($this->basePath . $languageFile['path']);
        }

        return $translations;
    }
}
