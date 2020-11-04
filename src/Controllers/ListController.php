<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;
use Illuminate\View\View;
use function array_merge;
use function config;
use function view;

class ListController
{
    public function index(): View
    {
        $config = config('inline-translations');

        return view('inline-translations::list', [
            'js' => AssetInjectionMiddleware::getJsRouteFromManifest('list.js'),
            'config' => $config,
        ]);
    }
}
