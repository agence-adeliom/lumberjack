# WP Hook

[![PHPUnit](https://github.com/agence-adeliom/lumberjack-hooks/actions/workflows/php.yml/badge.svg)](https://github.com/agence-adeliom/lumberjack-hooks/actions/workflows/php.yml)
[![Latest Unstable Version](https://poser.pugx.org/dugajean/wp-hook-annotations/v/unstable)](https://packagist.org/packages/agence-adeliom/lumberjack-hooks)
[![Total Downloads](https://poser.pugx.org/agence-adeliom/lumberjack-hooks/downloads)](https://packagist.org/packages/agence-adeliom/lumberjack-hooks) 
[![License](https://poser.pugx.org/agence-adeliom/lumberjack-hooks/license)](https://packagist.org/packages/agence-adeliom/lumberjack-hooks) 

Register WordPress hooks, filters and shortcodes.

*   With PHP Docblock (annotations)
*   Or with PHP 8.0 Attributes

## Requirements

*   PHP 7.1 or greater (tested on PHP 7.4, 8.0 and 8.1)

## Install

Via Composer

```bash
composer require agence-adeliom/lumberjack-hooks
```

## Usage

To automatically wire up your class, simply call the `HookRegistry::bootstrap` method, like so: 

```php
<?php

namespace My\CoolNamespace;

use Adeliom\Lumberjack\Hooks\HookRegistry;
use Adeliom\Lumberjack\Hooks\Models\Action;

class MyClass
{
    public function __construct(HookRegistry $hookRegistry) 
    {
        $hookRegistry->bootstrap($this);
    }
    
    /**
     * @Action(tag="init")    
     */
    #[Action(tag: "init")]
    public function doSomething()
    {
        // do something
    }
}
```

And you're done!

The following annotations can be used in PHP 7:

```php
/**
 * @Action(tag="the hook name", priority=1, accepted_args=1)
 * @Filter(tag="the filter name", priority=1, accepted_args=1)
 * @Shortcode(tag="the shortcode code")
 */
```

For PHP 8, please use attributes:

```php
#[Action(tag: "the hook name", priority: 1, accepted_args: 1)]
#[Filter(tag: "the filter name", priority: 1, accepted_args: 1)]
#[Shortcode(tag: "the shortcode code", priority: 1, accepted_args: 1)]
```

## Testing

```bash
composer test
```

## License
Lumberjack Hooks is released under [the MIT License](LICENSE).
