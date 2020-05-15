<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Requests\TranslationRequest;
use Antenna\InlineTranslations\TranslationFetcher;
use Antenna\InlineTranslations\TranslationUpdater;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function fetch(?string $language = null, TranslationFetcher $fetcher) : JsonResponse
    {
        if ($language === null) {
            $translations = $fetcher->fetchAllGroupedByKeys();
        } else {
            $translations = $fetcher->fetchByLanguage($language);
        }

        return new JsonResponse($translations);
    }

    public function upsert(TranslationRequest $request, TranslationUpdater $updater) : JsonResponse
    {
        $result = $updater->updateTranslation($request->key, $request->value, $request->language);

        return new JsonResponse(['result' => $result]);
    }
}
