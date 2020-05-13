<?php

declare(strict_types=1);

use Antenna\InlineTranslations\ApiController;
use Antenna\InlineTranslations\AssetController;

$config = array_merge(config('inline-translations.routes'), ['namespace' => 'Antenna\InlineTranslations']);
Route::group($config, static function ($router) : void {
    $router->get('all', [ApiController::class, 'fetch']);
    $router->get('all/{language}', [ApiController::class, 'fetch']);

    $router->get('assets/stylesheets', [AssetController::class, 'css'])->as('inline-translations.assets.css');
    $router->get('assets/javascript', [AssetController::class, 'js'])->as('inline-translations.assets.js');
});
