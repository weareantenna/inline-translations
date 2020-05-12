<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Interceptors;

use Illuminate\Translation\Translator;

class LaravelTranslatorInterceptor extends Translator
{
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        return '$$##' . $key . '##' . parent::get($key, $replace, $locale, $fallback) . '##$$';
    }
}
