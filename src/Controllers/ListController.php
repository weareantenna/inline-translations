<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;

class ListController
{
    public function index()
    {
        $config = config('inline-translations');
        $config = array_merge($config, config('localization'));

        return view('inline-translations::list', [
            'js' => AssetInjectionMiddleware::getJsRouteFromManifest('list.js'),
            'config' => $config
        ]);
    }
}
