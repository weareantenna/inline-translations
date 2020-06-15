# Inline translations manager

This package enables you to manage translations and actually translate your Laravel application inline.

We advice you not to use the package in production but manage translations within a staging environment and add an implementation 
to sync your translations to your production environment.


# Installation

Require this package in your composer.json and run composer update or

```
composer require weareantenna/inline-translations
```

After this update you'll need to


You can publish the configuration by running the command below. This will add the file `` where you can configure the package.


# Technical

## What can be translated inline?
This package comes out-of-the-box with the capability of managing the following translations

* Blade (the default)
* Vue

We would like to add other implementations in the future


# Usage

We have tried to not only to simplify the technical functionality of translation but also the workflow and management of the 
translations. 

This has been done by an inline "widget" that can be collapsed. If open, this visualizes the keys and (already defined) translations that can be found 
on the page for every language that has been configured (see `config/app.php`) within the application.


# Todo

There are some things we would like to add

* React translations
* An integration / combination with the translations manager by Barry vd Heuvel, see https://github.com/barryvdh/laravel-translation-manager