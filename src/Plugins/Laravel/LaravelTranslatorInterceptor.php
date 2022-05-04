<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Plugins\Laravel;

use Illuminate\Translation\Translator;

use function htmlentities;
use function is_string;

class LaravelTranslatorInterceptor extends Translator
{
    /**
     * @param string[] $replace
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true): string
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        if (is_string($translation)) {
            return '~~#' . $key . '#~#' . htmlentities($translation) . '#~~';
        }

        return '~~#' . $key . '#~~';
    }
}
