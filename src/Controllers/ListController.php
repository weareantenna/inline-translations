<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;
use Antenna\InlineTranslations\TranslationFetcher;
use Antenna\InlineTranslations\TranslationUpdater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function config;
use function view;

class ListController
{
    public function index(): View
    {
        $config = config('inline-translations');
        $config['exportUrl'] = route('inline-translations.export-csv');

        return view('inline-translations::list', [
            'js' => AssetInjectionMiddleware::getJsRouteFromManifest('list.js'),
            'config' => $config,
        ]);
    }

    public function export(TranslationFetcher $fetcher): StreamedResponse
    {
        $translations = $fetcher->fetchAllGroupedByKeys();
        $csv = Writer::createFromString();
        $csv->insertOne(['key', ...config('inline-translations')['supported-locales']]);
        foreach ($translations as $key => $translation) {
            $csv->insertOne([$key, ...array_values($translation)]);
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv->toString();
        }, 'translations.csv');
    }

    public function import(Request $request, TranslationUpdater $updater): JsonResponse
    {
        $csv = $request->file('translations');
        $csv = Reader::createFromFileObject($csv->openFile());
        $csv->setHeaderOffset(0);

        $result = ['result' => 'success', 'details' => []];
        foreach($csv->getRecords() as $record) {
            $key = $record['key'];
            unset($record['key']);

            $result['details'][$key] = [];
            foreach ($record as $lang => $translation) {
                $updater->updateTranslation($key, $translation, $lang);
                $result['details'][$key][$lang] = $translation;
            }
        }

        return new JsonResponse($result);
    }
}
