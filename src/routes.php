<?php

declare(strict_types=1);

use Antenna\InlineTranslations\ApiController;

$config = array_merge(config('inline-translations.routes'), ['namespace' => 'Antenna\InlineTranslations']);
Route::group($config, static function ($router) : void {
    $router->get('all', [ApiController::class, 'fetch']);
    $router->get('all/{language}', [ApiController::class, 'fetch']);
});
