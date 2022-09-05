<?php
declare(strict_types=1);

namespace App\PostTypes;

use Adeliom\Lumberjack\Pagination\Pagination;
use Rareloop\Lumberjack\Page as BasePage;

class Page extends BasePage
{
    use Pagination;

    public static function register(): void
    {
        return;
    }
}
