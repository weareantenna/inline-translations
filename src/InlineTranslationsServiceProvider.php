<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Console\ImportCommand;
use Antenna\InlineTranslations\Middleware\AssetInjectionMiddleware;
use Antenna\InlineTranslations\Plugins\Laravel\LaravelTranslatorInterceptor;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\Finder\Finder;

use function resource_path;

final class InlineTranslationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $filesystem = $this->getFilesystem();
        $basePath   = $this->getBasePath();
        $this->app->bind(TranslationFetcher::class, static function () use ($filesystem, $basePath): TranslationFetcher {
            return new TranslationFetcher($filesystem, $basePath);
        });
        $this->app->bind(TranslationUpdater::class, static function () use ($filesystem, $basePath): TranslationUpdater {
            return new TranslationUpdater($filesystem, $basePath);
        });

        $this->app->singleton(ImportCommand::class, static function ($app) {
            return new ImportCommand(
                (new Finder())
                    ->in($app->basePath())
                    ->exclude('storage')
                    ->exclude('vendor')
                    ->exclude('node_modules')
                    ->exclude('database')
                    ->name('*.php')
                    ->name('*.twig')
                    ->name('*.vue'),
                $app['config']['inline-translations.translation_functions'],
                $app[TranslationFetcher::class],
                $app[TranslationUpdater::class],
                $app['config']['app.locale']
            );
        });
        $this->commands([
            ImportCommand::class,
        ]);

        $this->publishes([
            __DIR__ . '/../config/inline-translations.php' => $this->app->configPath('inline-translations.php'),
        ], 'inline-translations');

        $this->publishes([
            __DIR__ . '/Plugins/Vue/js' => resource_path('assets/vendor/v-inline-translations'),
        ], 'inline-translations');

        if (! $this->isTranslationModeActive() || ! $this->allowedEnvironment()) {
            return;
        }

        /** @phpstan-ignore-next-line */
        if (! $this->app->environment($this->app['config']['inline-translations.translation_environments'])) {
            return;
        }

        $this->registerMiddleware(AssetInjectionMiddleware::class);

        $translationModeActive = $this->isTranslationModeActive();
        /** @phpstan-ignore-next-line */
        $this->app['view']->composer('inlineTranslations::index', static function (View $view) use ($translationModeActive): void {
            $view->with([
                'enabled' => (int) $translationModeActive,
            ]);
        });
    }

    public function register(): void
    {
        if (! $this->allowedEnvironment()) {
            return;
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/inline-translations.php', 'inline-translations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'inline-translations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/inline-translations'),
        ], 'inline-translations-views');

        $translationModeActive = $this->isTranslationModeActive();

        if (! $translationModeActive) {
            return;
        }

        $this->app->extend('translator', static function ($translator, $app): LaravelTranslatorInterceptor {
            $trans = new LaravelTranslatorInterceptor($translator->getLoader(), $app['config']['app.locale']);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    private function isTranslationModeActive(): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this->app['request']->cookie('inline-translations-active') === true;
    }

    private function allowedEnvironment(): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this->app->environment($this->app['config']['inline-translations.translation_environments']);
    }

    protected function getFilesystem(): Filesystem
    {
        $adapter = new LocalFilesystemAdapter($this->getBasePath());

        return new Filesystem($adapter);
    }

    protected function getBasePath(): string
    {
        /** @phpstan-ignore-next-line */
        return $this->app->basePath() . '/' . $this->app['config']['inline-translations.translation_folder'] . '/';
    }

    protected function registerMiddleware(string $middleware): void
    {
        /** @phpstan-ignore-next-line */
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
