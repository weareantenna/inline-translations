<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Exceptions\InvalidTranslationFileException;
use Antenna\InlineTranslations\Exceptions\TranslationUpdateException;
use Antenna\InlineTranslations\Models\TranslationKey;
use League\Flysystem\Filesystem;

use function array_filter;
use function array_replace_recursive;
use function array_reverse;
use function count;
use function file_exists;
use function is_array;
use function is_string;
use function var_export;

class TranslationUpdater
{
    private Filesystem $filesystem;
    private string $basePath;

    public function __construct(Filesystem $filesystem, string $basePath)
    {
        $this->filesystem = $filesystem;
        $this->basePath   = $basePath;
    }

    public function updateTranslation(string $key, ?string $value, string $language): bool
    {
        $key              = TranslationKey::fromString($key);
        $file             = $key->getTranslationFileForLanguage($language);
        $translationArray = $this->initializeTranslationFileForFile($file);
        $translationArray = $this->updateTranslationContent($translationArray, $key, $value);

        $fileContent = "<?php\n\nreturn " . var_export($translationArray, true) . ';';

        $this->filesystem->write($file, $fileContent);

        return true;
    }

    public function removeTranslation(string $key, string $language): bool
    {
        $key              = TranslationKey::fromString($key);
        $file             = $key->getTranslationFileForLanguage($language);
        $translationArray = $this->initializeTranslationFileForFile($file);

        $subKeys      = $key->getKeyAsArray();
        $currentValue = &$translationArray;
        foreach ($subKeys as $i => $subKey) {
            if (is_string($currentValue) || ! isset($currentValue[$subKey])) {
                throw TranslationUpdateException::unableToRemoveKey($key);
            }

            if ($i === count($subKeys) - 1) {
                unset($currentValue[$subKey]);
                continue;
            }

            $currentValue = &$currentValue[$subKey];
        }

        $translationArray = $this->recursiveFilter($translationArray);
        $fileContent      = "<?php\n\nreturn " . var_export($translationArray, true) . ';';

        $this->filesystem->write($file, $fileContent);

        return true;
    }

    /**
     * @param mixed[] $input
     *
     * @return mixed[]
     */
    private function recursiveFilter(array $input): array
    {
        foreach ($input as &$value) {
            if (! is_array($value)) {
                continue;
            }

            $value = $this->recursiveFilter($value);
        }

        return array_filter($input);
    }

    /** @return array<string,array<string, string>> */
    private function initializeTranslationFileForFile(string $file): array
    {
        $translationArray = [];
        if (file_exists($this->basePath . '/' . $file)) {
            $translationArray = require $this->basePath . '/' . $file;
        }

        if (! is_array($translationArray)) {
            throw InvalidTranslationFileException::noArray($this->basePath . '/' . $file);
        }

        return $translationArray;
    }

    /**
     * @param array<array<string, mixed>|string> $content
     *
     * @return array<array<string, mixed>|string>
     */
    private function updateTranslationContent(array $content, TranslationKey $key, ?string $value): array
    {
        $subKeys        = $key->getKeyAsArray();
        $newTranslation = $value;
        foreach (array_reverse($subKeys) as $i => $subKey) {
            $newTranslation = [$subKey => $newTranslation];
        }

        if (! is_array($newTranslation)) {
            throw TranslationUpdateException::unableToMergeTranslations($key, $value ?? '');
        }

        return array_replace_recursive($content, $newTranslation);
    }
}
