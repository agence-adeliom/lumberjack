# [READ-ONLY] Lumberjack Cron

Register WordPress scheduled tasks.

## Requirements

* PHP 8.0 or greater
* Composer
* Lumberjack

## Installation

```bash
composer require agence-adeliom/lumberjack-cron

# Copy the configuration file
cp vendor/agence-adeliom/lumberjack-cron/config/crons.php web/app/themes/YOUR_THEME/config/crons.php
```
#### Register the service provider into web/app/themes/YOUR_THEME/config/app.php

```php
'providers' => [
    ...
    \Adeliom\Lumberjack\Cron\CronProvider::class
]
```

## Usage

```php
<?php

namespace App\Tasks;

use Adeliom\Lumberjack\Cron\Cron;

class ExampleCronEvent extends Cron
{
    public $every = [
        'seconds'   => 30,
        'minutes'   => 15,
        'hours'     => 1,
    ];
    
    public function handle(){
        update_option('last_ran', current_time('mysql'));
    }
}
```

Register the task into your config file `web/app/themes/YOUR_THEME/config/crons.php` :

```php
return [
    'register' => [
        ...
        App\Tasks\ExampleCronEvent::class
    ],
];
```

## License
Lumberjack Cron is released under [the MIT License](LICENSE).
