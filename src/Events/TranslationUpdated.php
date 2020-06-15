<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Events;

use Antenna\InlineTranslations\Models\TranslationKey;

class TranslationUpdated
{
    public TranslationKey $key;
    public string $newValue;
    public string $locale;

    public function __construct(TranslationKey $key, $newValue, $locale)
    {
        $this->key      = $key;
        $this->newValue = $newValue;
        $this->locale   = $locale;
    }
}
