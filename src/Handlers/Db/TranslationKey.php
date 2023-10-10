<?php

namespace Antenna\InlineTranslations\Handlers\Db;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    protected $attributes = [
        'key' => null,
    ];

    public function languageLines(): HasMany
    {
        return $this->hasMany(LanguageLine::class);
    }
}
