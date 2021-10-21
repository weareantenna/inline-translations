<?php

return [
    'routes' => [
        /**
         * All paths of this package will be prefixed with the value defined in this parameter.
         * By default following paths can be used to activate or disable the tool:
         *   - /{prefix}/enable
         *   - /{prefix}/disable
         */
        'prefix'     => 'inline-translations',
        /**
         * Underneath you can define middleware that will be added to all routes of this package.
         * It might be sensible to add some kind of authentication in this middleware.
         */
        'middleware' => ['web'],
        /**
         * Underneath you can define the redirect url to which you want to redirect,
         * once translation mode has been (de)activated
         */
        'redirect_url' => '/',
    ],
    /**
     * This is an array of the locales that your application needs to support.
     * This list is used to define the possible translations
     */
    'supported-locales' => ['en'],
    /**
     * The location where your translations are found, is defined in this parameter.
     * Laravel by default stores this in resources/lang
     */
    'translation_folder' => 'resources/lang',
    /**
     * This parameter defines the width of the widget when it is opened. This can be changed depending
     * on the amount of languages you are supporting, in order to get a better overview.
     */
    'widget_width' => 350,
    /**
     * Here you can define on which environments, translations are possible.
     * When the active environment is not in the array underneath, the tool will not be available.
     */
    'translation_environments' => ['local'],

    /**
     * Here you can define which functions are being used to translate keys.
     * This is used in the artisan command that fetches new keys from your source files
     */
    'translation_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
        '$t',
        '__t'
    ]
];
