# [READ-ONLY] Lumberjack Hooks

Register WordPress hooks, filters and shortcodes with PHP Attributes

## Requirements

* PHP 8.0 or greater
* Composer
* Lumberjack

## Installation

```bash
composer require agence-adeliom/lumberjack-hooks

# Copy the configuration file
cp vendor/agence-adeliom/lumberjack-hooks/config/hooks.php web/app/themes/YOUR_THEME/config/hooks.php
```

#### Register the service provider into web/app/themes/YOUR_THEME/config/app.php

```php
'providers' => [
    ...
    \Adeliom\Lumberjack\Hooks\HookProvider::class
]
```

## Usage

Create your hook class :

```php
<?php

namespace App\Hooks;

use Adeliom\Lumberjack\Hooks\Models\Action;
use Adeliom\Lumberjack\Hooks\Models\Filter;

class MyClass
{

    #[Action(tag: "init")]
    public function doSomethingAtInit()
    {
        // do something
    }
    
    #[Filter(tag: "enter_title_here")]
    public function alterEnterTitleHere()
    {
        // do something
    }
}
```

Register the class into your config file `web/app/themes/YOUR_THEME/config/hooks.php` :

```php
return [
    'register' => [
        ...
        App\Hooks\MyClass::class
    ],
];
```

And you're done!

# API

```php
#[Action(tag: "the hook name", priority: 1, accepted_args: 1)]
#[Filter(tag: "the filter name", priority: 1, accepted_args: 1)]
#[Shortcode(tag: "the shortcode code", priority: 1, accepted_args: 1)]
```

## License
Lumberjack Hooks is released under [the MIT License](LICENSE).
