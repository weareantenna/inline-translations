<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Exceptions;

use Exception;

class InvalidKeyException extends Exception
{
    public static function providedKeyInvalid(string $key): self
    {
        return new self('The translation key "' . $key . '" is invalid, and could not be parsed.');
    }

    public static function providedKeyForPackageInvalid(string $key, ?string $package): self
    {
        if (! $package) {
            return self::providedKeyInvalid($key);
        }

        return new self(
            'The translation key "' . $key . '" for package "' .
            $package . '" is invalid, and could not be parsed.'
        );
    }
}
