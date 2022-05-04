<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Exceptions;

use Exception;

class InvalidTranslationFileException extends Exception
{
    public static function noArray(string $file): self
    {
        return new self('The translation file located in "' . $file . '" is invalid, it does not contain an array');
    }
}
