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

#### Register the service provider into web/app/themes/YOUR_THEME/config/app.php

```php
'providers' => [
    ...
    \Adeliom\Lumberjack\Assets\AssetsProvider::class
]
```

## Usage


## License
Lumberjack Assets is released under [the MIT License](LICENSE).
