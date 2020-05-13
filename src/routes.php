<?php

declare(strict_types=1);

use Antenna\InlineTranslations\Controllers\ApiController;
use Antenna\InlineTranslations\Controllers\AssetController;

$config = array_merge(config('inline-translations.routes'), ['namespace' => 'Antenna\InlineTranslations']);
Route::group($config, static function ($router) : void {
    $router->get('all', [ApiController::class, 'fetch']);
    $router->get('all/{language}', [ApiController::class, 'fetch']);

    $router->get('assets/stylesheets', [AssetController::class, 'css'])->name('inline-translations.assets.css');
    $router->get('assets/javascript', [AssetController::class, 'js'])->name('inline-translations.assets.js');
});
