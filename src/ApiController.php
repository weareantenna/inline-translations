<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function fetch(string $language = null, TranslationFetcher $fetcher) {
        if ($language === null) {
            $translations = $fetcher->fetchAll();
        } else {
            $translations = $fetcher->fetchByLanguage($language);
        }
        return new JsonResponse($translations);
    }
}
