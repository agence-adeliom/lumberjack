# [READ-ONLY] Lumberjack Taxonomy

Implementation of taxonomies into Lumberjack

## Requirements

* PHP 8.0 or greater
* Composer
* Lumberjack

## Installation

```bash
composer require agence-adeliom/lumberjack-taxonomy

# Copy the configuration file
cp vendor/agence-adeliom/lumberjack-admin/config/taxonomies.php web/app/themes/YOUR_THEME/config/taxonomies.php
```

#### Register the service provider into web/app/themes/YOUR_THEME/config/app.php

```php
'providers' => [
    ...
    \Adeliom\Lumberjack\Taxonomy\CustomTaxonomyServiceProvider::class
]
```

## Usage


```php
<?php

namespace App\Taxonomies;

use Adeliom\Lumberjack\Taxonomy\Term as BaseTerm;

class Project extends BaseTerm
{
    /**
     * Return the key used to register the taxonomy with WordPress
     * First parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return string|null
     */
    public static function getTaxonomyType(): ?string
    {
        return 'project';
    }

    /**
     * Return the object type which use this taxonomy.
     * Second parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return array|null
     */
    public static function getTaxonomyObjectTypes(): ?array
    {
        return ['post'];
    }

    /**
     * Return the config to use to register the taxonomy with WordPress
     * Third parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return array|null
     */
    protected static function getTaxonomyConfig(): ?array
    {
        return array( 
            'labels' => [
                'name' => 'Projects',
                'new_item_name' => 'New project'
            ],
            'public' => true, 
            'show_in_rest' => true,
            'hierarchical' => false, 
        );
    }
}
```

Register the term into your config file `web/app/themes/YOUR_THEME/config/taxonomies.php` :

```php
return [
    'register' => [
        ...
        App\Taxonomies\Project::class
    ],
];
```

## License
Lumberjack Taxonomy is released under [the MIT License](LICENSE).
