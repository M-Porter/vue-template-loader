# Vue Template Loader

Laravel package which will download your html templates as built by webpack-html-loader for use in your projects.

## Installation

Require this package with composer.

- Laravel 6, 7
    ```
    composer require m-porter/vue-template-loader "^6.0.0"
    ```

- Laravel ^5.5,<5.9
    ```
    composer require m-porter/vue-template-loader "^0.2.0"
    ```

### Laravel 5.5+:

If you don't use auto-discovery, add the `ServiceProvider` to the providers array in `config/app.php`.

```
MPorter\VueTemplateLoader::class,
```

Copy the package config to your local config with the publish command:

```
php artisan vendor:publish --provider="MPorter\VueTemplateLoader\ServiceProvider"
```
