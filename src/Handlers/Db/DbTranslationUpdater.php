<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Handlers\Db;

use Antenna\InlineTranslations\TranslationUpdater;

class DbTranslationUpdater implements TranslationUpdater
{
    public function updateTranslation(string $key, ?string $value, string $language): bool
    {
        $key = TranslationKey::where('key', $key)->first();
        $line = $key->languageLines()->where('language', $language)->first();

        $line->translation = $value;
        return $line->save();
    }

    public function removeTranslation(string $key, string $language): bool
    {
        $key = TranslationKey::where('key', $key)->first();
        $line = $key->languageLines()->where('language', $language)->first();

        return $line->delete();
    }
}
