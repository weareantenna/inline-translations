<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Events\TranslationUpdated;
use Antenna\InlineTranslations\Requests\TranslationRequest;
use Antenna\InlineTranslations\TranslationFetcher;
use Antenna\InlineTranslations\TranslationUpdater;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

use function opcache_reset;

class ApiController extends BaseController
{
    public function fetch(TranslationFetcher $fetcher, ?string $language = null): JsonResponse
    {
        if ($language === null) {
            $translations = $fetcher->fetchAllGroupedByKeys();
        } else {
            $translations = $fetcher->fetchByLanguage($language);
        }

        return new JsonResponse($translations);
    }

    public function upsert(TranslationRequest $request, TranslationUpdater $updater): JsonResponse
    {
        /** @phpstan-ignore-next-line */
        $result = $updater->updateTranslation($request->key, $request->value, $request->language);
        opcache_reset();

        return new JsonResponse(['result' => $result]);
    }

    public function triggerUpdateEvent(Dispatcher $events): JsonResponse
    {
        return new JsonResponse(['results' => $events->dispatch(new TranslationUpdated())]);
    }
}
