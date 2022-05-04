<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Exceptions;

use Antenna\InlineTranslations\Models\TranslationKey;
use Exception;

class TranslationUpdateException extends Exception
{
    public static function unableToMergeTranslations(TranslationKey $key, string $value): self
    {
        return new self(
            'We were unable to fill in translation key "' . $key->getKey() . '" with value "' . $value . '"'
        );
    }

    public static function unableToRemoveKey(TranslationKey $key): self
    {
        return new self(
            'We were unable to remove translation key "' . $key->getKey() . '"'
        );
    }
}
