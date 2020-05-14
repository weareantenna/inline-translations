<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;
use Antenna\InlineTranslations\Plugins\Laravel\LaravelTranslatorInterceptor;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use function base_path;
use function resource_path;

final class InlineTranslationsServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->publishes([
            __DIR__ . '/../config/inline-translations.php' => $this->app->configPath('inline-translations.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/inlineTranslations'),
        ], 'views');
        $this->registerMiddleware(AssetInjectionMiddleware::class);

        $this->publishes([
            __DIR__ . '/Plugins/Vue/js' => resource_path('assets/vendor/v-inline-translations'),
        ], 'vue-assets');
    }

    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/inline-translations.php', 'inline-translations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'inlineTranslations');

        $translationModeActive = $this->app['request']->query($this->app['config']['inline-translations.url_query']) === 'true';
        $this->app['view']->composer('inlineTranslations::index', static function (View $view) use ($translationModeActive) : void {
            $view->with([
                'enabled' => (int) $translationModeActive,
            ]);
        });

        $filesystem = $this->getFilesystem();
        $this->app->bind(TranslationFetcher::class, static function () use ($filesystem) : TranslationFetcher {
            return new TranslationFetcher($filesystem);
        });
        $this->app->bind(TranslationUpdater::class, static function () use ($filesystem) : TranslationUpdater {
            return new TranslationUpdater($filesystem);
        });

        if (! $translationModeActive) {
            return;
        }

        $this->app->extend('translator', static function ($translator, $app) : LaravelTranslatorInterceptor {
            $trans = new LaravelTranslatorInterceptor($translator->getLoader(), $app['config']['app.locale']);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    protected function getFilesystem() : Filesystem
    {
        $languagePath = $this->app->basePath() . '/' . $this->app['config']['inline-translations.translation_folder'];
        $adapter      = new Local($languagePath);

        return new Filesystem($adapter);
    }

    protected function registerMiddleware(string $middleware) : void
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
