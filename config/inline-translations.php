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
        'middleware' => null,
    ],
    /**
     * The location where your translations are found, is defined in this parameter.
     * Laravel by default stores this in resources/lang
     */
    'translation_folder' => 'resources/lang',
    /**
     * This parameter defines the width of the widget when it is opened. This can be changed depending
     * on the amount of languages you are supporting, in order to get a better overview.
     */
    'widget_width' => 350
];
