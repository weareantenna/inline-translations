<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Interceptors\LaravelTranslatorInterceptor;
use Illuminate\Translation\TranslationServiceProvider;

final class InlineTranslationsServiceProvider extends TranslationServiceProvider
{
    public function boot() : void
    {
        $this->publishes([
            __DIR__ . '/config/inline-translations.php' => config_path('inline-translations.php'),
        ]);
        $this->mergeConfigFrom(__DIR__ . '/config/inline-translations.php', 'inline-translations');
    }

    public function register() : void
    {
        // @phpstan-ignore-next-line
        if ($this->app->request->query(config('inline-translations.url_query')) !== 'true') {
            parent::register();

            return;
        }

        $this->registerLoader();
        $this->app->singleton('translator', static function ($app): LaravelTranslatorInterceptor {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new LaravelTranslatorInterceptor($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
