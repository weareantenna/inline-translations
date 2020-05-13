<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Interceptors\LaravelTranslatorInterceptor;
use Antenna\InlineTranslations\Middleware\InjectTranslator;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\View;
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

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/inlineTranslations'),
        ], 'views');
        $this->registerMiddleware(InjectTranslator::class);

        $this->publishes([
            __DIR__ . '/Plugins/Vue/js' => resource_path('assets/vendor/v-inline-translations')
        ], 'vue-assets');
    }

    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/inline-translations.php', 'inline-translations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'inlineTranslations');

        $translationModeActive = $this->app['request']->query($this->app['config']['inline-translations.url_query']) === 'true';
        $this->app['view']->composer('inlineTranslations::index', static function (View $view) use ($translationModeActive) {
            $view->with([
                'enabled' => (int) $translationModeActive
            ]);
        });

        $languagePath = $this->app->basePath() . '/' . $this->app['config']['inline-translations.translation_folder'];
        $this->app->bind(TranslationFetcher::class, static function () use ($languagePath) {
            $adapter    = new Local($languagePath);
            $filesystem = new Filesystem($adapter);

            return new TranslationFetcher($filesystem);
        });

        if (!$translationModeActive) {
            return;
        }

        $this->app->extend('translator', static function ($translator, $app) : LaravelTranslatorInterceptor {
            $trans = new LaravelTranslatorInterceptor($translator->getLoader(), $app['config']['app.locale']);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    protected function registerMiddleware(string $middleware): void
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
