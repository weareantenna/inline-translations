<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Interceptors;

use Illuminate\Translation\Translator;
use function is_string;

class LaravelTranslatorInterceptor extends Translator
{
    /** @phpstan-ignore-next-line */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        if (is_string($translation)) {
            return '~~#' . $key . '#~#' . $translation . '#~~';
        }

        return '~~#' . $key . '#~~';
    }
}
