# [READ-ONLY] Lumberjack Pagination

Implementation of pagination into Lumberjack

## Requirements

* PHP 8.0 or greater
* Composer
* Lumberjack

## Installation

```bash
composer require agence-adeliom/lumberjack-pagination
```

## Usage

Add the trait into your post type

```php
<?php

namespace App\PostTypes;

use Rareloop\Lumberjack\Post as BasePost;
use Adeliom\Lumberjack\Pagination\Pagination;

class Post extends BasePost
{
    use Pagination;
}
```

Now your can use it into your controllers

```php
<?php
namespace App;

use Adeliom\Lumberjack\Pagination\PaginationViewModel;
use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use App\PostTypes\Post;
use Timber\Timber;

class IndexController extends Controller
{
    public const RESULTS_PER_PAGE = 10;

    public function handle(): TimberResponse
    {
        global $paged;
        if (!isset($paged) || !$paged) {
            $paged = 1;
        }
        
        $context = Timber::get_context();
        $context['posts'] = Post::paginate(self::RESULTS_PER_PAGE);
        $context['pagination'] = PaginationViewModel::fromQueryBuilder(self::RESULTS_PER_PAGE, $paged);
        
        return new TimberResponse(['page/index.html.twig'], $context);
    }
}
```

## License
Lumberjack Pagination is released under [the MIT License](LICENSE).
