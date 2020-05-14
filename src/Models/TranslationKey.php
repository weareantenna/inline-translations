<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Models;

use Antenna\InlineTranslations\Exceptions\InvalidKeyException;
use function array_shift;
use function count;
use function explode;
use function strpos;

class TranslationKey
{
    private ?string $package;
    private string $key;

    public function __construct(string $key, ?string $package = null)
    {
        $this->package = $package;
        $this->setKey($key);
    }

    public static function fromString(string $fullKey) : self
    {
        $split   = explode('::', $fullKey);
        $package = count($split) > 1 ? array_shift($split) : null;
        $key     = array_shift($split);

        if ($key === null) {
            throw InvalidKeyException::providedKeyInvalid($fullKey);
        }

        return new self($key, $package);
    }

    private function setKey(string $key) : void
    {
        if (strpos($key, '.') === false) {
            throw InvalidKeyException::providedKeyForPackageInvalid($key, $this->package);
        }

        $this->key = $key;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getTranslationFileForLanguage(string $language) : string
    {
        $path = '';
        if ($this->package) {
            $path .= 'vendor/' . $this->package . '/';
        }

        $path .= $language . '/' . explode('.', $this->key)[0] . '.php';

        return $path;
    }

    /** @return array<string> */
    public function getKeyAsArray() : array
    {
        $keyArray = explode('.', $this->key);
        array_shift($keyArray);

        return $keyArray;
    }
}
