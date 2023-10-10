<?php

namespace Antenna\InlineTranslations\Handlers\Db;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageLine extends Model
{
    protected $attributes = [
        'language' => null,
        'translation' => null,
    ];

    protected $touches = ['translationKey'];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
