<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;
use Antenna\InlineTranslations\TranslationFetcher;
use Antenna\InlineTranslations\TranslationUpdater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use League\Csv\Reader;
use League\Csv\Writer;
use SplFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function array_values;
use function assert;
use function config;
use function is_array;
use function response;
use function route;
use function view;

class ListController
{
    public function index(): View
    {
        $config = config('inline-translations');
        assert(is_array($config));
        $config['exportUrl'] = route('inline-translations.export-csv');

        /** @phpstan-ignore-next-line */
        return view('inline-translations::list', [
            'js' => AssetInjectionMiddleware::getJsRouteFromManifest('list.js'),
            'config' => $config,
        ]);
    }

    public function export(TranslationFetcher $fetcher): StreamedResponse
    {
        $translations = $fetcher->fetchAllGroupedByKeys();
        $csv          = Writer::createFromString();

        /** @phpstan-ignore-next-line */
        $csv->insertOne(['key', ...config('inline-translations')['supported-locales']]);
        foreach ($translations as $key => $translation) {
            $csv->insertOne([$key, ...array_values($translation)]);
        }

        return response()->streamDownload(static function () use ($csv): void {
            echo $csv->toString();
        }, 'translations.csv');
    }

    public function import(Request $request, TranslationUpdater $updater): JsonResponse
    {
        $csv = $request->file('translations');
        assert($csv instanceof UploadedFile);

        $file = $csv->openFile();
        assert($file instanceof SplFileObject);
        $csv = Reader::createFromFileObject($file);
        $csv->setHeaderOffset(0);

        $result = ['result' => 'success', 'details' => []];

        /** @var array{key: string, value: string} $record */
        foreach ($csv->getRecords() as $record) {
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
