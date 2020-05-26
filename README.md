# Vue Template Loader

Laravel package which will download your html templates as built by webpack-html-loader for use in your projects.

Intended for use with [vue-cli](https://cli.vuejs.org/).

## Installation

Require this package with composer.

```
composer require m-porter/vue-template-loader
```

| Laravel version | This package version |
| --- | --- |
| ^6.0&#124;^7.0 | ^6.0.0 |
| ^5.5,<5.9 | ^0.2.0 |

### Package Discovery

If you don't use auto-discovery, add the `ServiceProvider` to the providers array in `config/app.php`.

```
MPorter\VueTemplateLoader::class,
```

### Configuration

Copy the package config to your local config with artisan's vendor:publish command:

```
php artisan vendor:publish --provider="MPorter\VueTemplateLoader\ServiceProvider"
```

## Usage

This package is very opinionated and requires changes to your default `vue.config.js` file. Vue-template-loader is intended to be used with vue-cli's multi-page mode.

You can assume the following laravel project structure for this usage tutorial. (Modified directory structure after running `vue create frontend`).

```
<laravel project root>/frontend
├── package-lock.json
├── package.json
├── src
│   ├── example-app
│   │   ├── App.vue
│   │   ├── assets
│   │   │   └── logo.png
│   │   ├── components
│   │   │   └── HelloWorld.vue
│   │   ├── index.blade.php
│   │   └── main.js
└── vue.config.js
```


- Remove the default `app` entry with `chainWebpack`.

    ```diff
    // vue.config.js

    module.exports = {

    +    chainWebpack: (config) => {
    +        config.entryPoints.delete('app');
    +    },

    };
    ```

- Update `outputDir` to point at laravel's `public` directory.

    ```diff
    // vue.config.js

    module.exports = {

    +    outputDir: '../public/assets',

        chainWebpack: (config) => {
            config.entryPoints.delete('app');
        },

    };
    ```

- Update `publicPath` for both local development and prod. This will allow you to use both hmr and built files locally.

    `NODE_ENV` will already exist on `process.env` but `WEBPACK_HOST` and `WEBPACK_PORT` will not. You will need to either add it to your npm scripts (e.g. `WEBPACK_HOST=0.0.0.0 vue-cli-service serve`) or use a npm package like `dotenv` to read your laravel `.env` file.

    ```diff
    // vue.config.js

    + const isProd = process.env.NODE_ENV === 'production';
    + const host = process.env.WEBPACK_HOST || '0.0.0.0';
    + const port = process.env.WEBPACK_PORT || 8080;

    module.exports = {

    +    publicPath: isProd ? '/assets' : `http://${host}:${port}/`,

        outputDir: '../public/assets',

        chainWebpack: (config) => {
            config.entryPoints.delete('app');
        },

    };
    ```

- Update `pages` to include your frontend app. For this example, you can assume the following app structure.

    **NOTE: Your folder _CANNOT_ be named `app`. This is a reserved folder name in vue-cli.**

    ```diff
    // vue.config.js

    const isProd = process.env.NODE_ENV === 'production';
    const host = process.env.WEBPACK_HOST || '0.0.0.0';
    const port = process.env.WEBPACK_PORT || 8080;

    + const resourcePath = (n) => path.join('../../resources/views/vue', `${n}.blade.php`);
    + const filenameForEnv = (n) => (isProd ? resourcePath(n) : `${n}.html`);

    module.exports = {

    +    pages: {
    +        'example-app': {
    +            title: 'Example App',
    +            entry: 'src/example-app/main.js',
    +            template: 'src/example-app/index.blade.php',
    +            filename: filenameForEnv('example-app'),
    +        },
    +    },

        publicPath: isProd ? '/assets' : `http://${host}:${port}/`,

        outputDir: '../public/assets',

        chainWebpack: (config) => {
            config.entryPoints.delete('app');
        },

    };
    ```

    Two helper functions, `resourcePath` and `filenameForEnv`, were added to the config to help manage output file naming based on the current environment.

    `resourcePath` handles sending the built html template to the vue-template-loader expected `resource_path('views/vue')` directory it reads from on the PHP side.

- Test it out! Run `npm run serve` from your frontend directory and modify your `routes/web.php`.

    ```php
    // routes/web.php

    use Illuminate\Support\Facades\Route;
    use MPorter\VueTemplateLoader\Loader;

    Route::get('/', function () {
        return view(Loader::getTemplate('example-app'));
    });
    ```

    You should now be able to see the default vue page!

- Update your `.gitignore` to avoid checking in built templates and files.

    ```gitignore
    public/assets/
    resources/views/vue/
    ```
