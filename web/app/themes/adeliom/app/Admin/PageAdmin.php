<?php

namespace App\Admin;

use Adeliom\Lumberjack\Admin\AbstractAdmin;
use Adeliom\Lumberjack\Admin\Fields\Tabs\MediaTab;
use Traversable;

/**
 * Class PageAdmin
 * @package App\Admin
 */
class PageAdmin extends AbstractAdmin
{
    const TITLE = "Toto";

    /**
     * @return string
     */
    public static function getTitle(): string
    {
        return "page";
    }

    public static function getStyle(): string
    {
        return 'default';
    }

    public static function getMenuOrder(): int
    {
        return 0;
    }

    public static function getHideOnScreen(): array
    {
        return [
            "the_content",
        ];
    }

    /**
     * @see https://github.com/wordplate/extended-acf#fields
     * @return Traversable
     */
    public static function getFields(): Traversable
    {
        yield MediaTab::make()->media();
    }

    /**
     * @see https://github.com/wordplate/extended-acf#location
     * @return Traversable
     */
    public static function getLocation(): Traversable
    {
        yield \Extended\ACF\Location::where('post_type', "page");
    }
}
