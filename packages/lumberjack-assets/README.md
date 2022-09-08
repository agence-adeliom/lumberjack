# [READ-ONLY] Lumberjack Assets

Implementation of taxonomies into Lumberjack

## Requirements

* PHP 8.0 or greater
* Composer
* Lumberjack
* npm 8.0 or greater

## Installation

```bash
composer require agence-adeliom/lumberjack-assets

# Copy the configuration file
cp vendor/agence-adeliom/lumberjack-assets/config/assets.php web/app/themes/YOUR_THEME/config/assets.php
```

### Using Webpack Encore

```bash
cd web/app/themes/YOUR_THEME/
npm install @symfony/webpack-encore --save-dev
```

#### Creating the webpack.config.js File

Next, create a new `webpack.config.js` file. You can also check the documentation [https://symfony.com/doc/current/frontend.html#encore-documentation](https://symfony.com/doc/current/frontend.html#encore-documentation)

```js
const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
```

#### Register the service provider into web/app/themes/YOUR_THEME/config/app.php

```php
'providers' => [
    ...
    \Adeliom\Lumberjack\Assets\AssetsProvider::class
]
```

## Usage

## Backend usage

### Enqueue assets

```php
add_action( 'wp_enqueue_scripts', function () {
	\Adeliom\Lumberjack\Assets\Assets::enqueue( 'custom-asset', 'your-entrypoint', [
        'version' => null,
        'deps' => [],
        'in_footer' => true,
        'media' => 'all',
        'attributes' => []
    ]);
});
```

## Frontend usage

```php
{{ entry_script_tags('your-entrypoint') }}
# Add custom attributes
{{ entry_script_tags('your-entrypoint', {'async': true}) }}

{{ entry_link_tags('your-entrypoint') }}
# Add custom attributes
{{ entry_link_tags('your-entrypoint', {'media': 'print'}) }}

# Check if the entrypoint exist
{{ entry_exists('your-entrypoint')}}

# Get the path of your asset
{{ asset('images/logo.svg') }}
```

## License
Lumberjack Assets is released under [the MIT License](LICENSE).
