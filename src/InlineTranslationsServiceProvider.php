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

    public function register()
    {
        if ($this->app->request->query(config('inline-translations.url_query')) !== 'true') {
            return parent::register();
        }

        $this->registerLoader();
        $this->app->singleton('translator', static function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new LaravelTranslatorInterceptor($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
