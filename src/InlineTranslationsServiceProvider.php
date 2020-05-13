<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Interceptors\LaravelTranslatorInterceptor;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

final class InlineTranslationsServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->publishes([
            __DIR__ . '/config/inline-translations.php' => $this->app->configPath('inline-translations.php'),
        ]);
    }

    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/inline-translations.php', 'inline-translations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $languagePath = $this->app->basePath() . '/' . $this->app['config']['inline-translations.translation_folder'];
        $this->app->bind(TranslationFetcher::class, static function () use ($languagePath) {
            $adapter    = new Local($languagePath);
            $filesystem = new Filesystem($adapter);

            return new TranslationFetcher($filesystem);
        });

        if ($this->app['request']->query($this->app['config']['inline-translations.url_query']) !== 'true') {
            return;
        }

        $this->app->extend('translator', static function ($translator, $app) : LaravelTranslatorInterceptor {
            $trans = new LaravelTranslatorInterceptor($translator->getLoader(), $app['config']['app.locale']);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
