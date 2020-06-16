# Inline translations manager

![Screenshot of inline translation tool][screenshot]

This package enables you to manage translations and actually translate your Laravel application inline.

We advise you not to use this package in production environments. Translations can be managed within staging or development environments and synced to production with a custom implementation (or via your git repository).


## Installation

Add the package as a dependency in your project:
```
composer require antenna/inline-translations
```

You must publish the configuration by running the command below. This will add the file `config/inline-translations.php` where you can configure the package. A Vue component will be published as well (`resources/assets/vendor/v-inline-translations/app.js`). You can use this as a Vue plugin for your vue instance. This is only needed if you use translations in your Vue components.
```
php artisan vendor:publish --provider="Antenna\InlineTranslations\InlineTranslationsServiceProvider"
```

(Optional) use the Vue plugin. the `methodName` parameter defines the translation function that you use within your Vue components. This function will be overwritten by this package (when translation mode is active):
```
import VInlineTranslations from "resources/assets/vendor/v-inline-translations/app";
Vue.use(VInlineTranslations, {methodName: '__t'});
new Vue({
  ...
});
```

## Configuration
This package has some configuration options. All configuration parameters have default values, so this package should work out of the box. Underneath you'll find the default configuration with explanation for all available parameters
```
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
    /**
     * Here you can define on which environments, translations are possible.
     * When the active environment is not in the array underneath, the tool will not be available.
     */
    'translation_environments' => ['local', 'staging']
];
```

## Technical

### What can be translated inline?
This package comes out-of-the-box with the capability of managing the following translations

* Blade (the default)
* Vue

We would like to add other implementations in the future


## Usage

We have tried to not only simplify the technical functionality of translation but also the workflow and management of the
translations.

This has been done by an inline "widget" that can be collapsed. If open, this visualizes the keys and (already defined) translations that can be found
on the page for every language that has been configured (see `config/app.php`) within the application.


## Todo

There are some things we would like to add in the future

* React translations
* An integration / combination with the [translations manager by Barry vd Heuvel](https://github.com/barryvdh/laravel-translation-manager)


[screenshot]: https://i.ibb.co/s5P23Ft/screely-1592219274102.png "Translator screenshot"
