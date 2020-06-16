<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Exceptions\InvalidTranslationFileException;
use Antenna\InlineTranslations\Exceptions\TranslationUpdateException;
use Antenna\InlineTranslations\Models\TranslationKey;
use League\Flysystem\Filesystem;
use function array_replace_recursive;
use function array_reverse;
use function is_array;
use function var_export;

class TranslationUpdater
{
    private Filesystem $filesystem;
    private string $basePath;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->basePath   = $this->filesystem->getAdapter()->getPathPrefix(); //@phpstan-ignore-line
    }

    public function updateTranslation(string $key, string $value, string $language) : bool
    {
        $key              = TranslationKey::fromString($key);
        $file             = $key->getTranslationFileForLanguage($language);
        $translationArray = [];

        if (file_exists($this->basePath . '/' . $file)) {
            $translationArray = require $this->basePath . '/' . $file;
        }

        if (! is_array($translationArray)) {
            throw InvalidTranslationFileException::noArray($this->basePath . '/' . $file);
        }

        $translationArray = $this->updateTranslationContent($translationArray, $key, $value);
        $fileContent      = "<?php\n\nreturn " . var_export($translationArray, true) . ';';

        return $this->filesystem->put($file, $fileContent);
    }

    /**
     * @param array<array|string> $content
     *
     * @return array<array|string>
     */
    private function updateTranslationContent(array $content, TranslationKey $key, string $value) : array
    {
        $subKeys        = $key->getKeyAsArray();
        $newTranslation = $value;
        foreach (array_reverse($subKeys) as $i => $subKey) {
            $newTranslation = [$subKey => $newTranslation];
        }

        if (! is_array($newTranslation)) {
            throw TranslationUpdateException::unableToMergeTranslations($key, $value);
        }

        return array_replace_recursive($content, $newTranslation);
    }
}
