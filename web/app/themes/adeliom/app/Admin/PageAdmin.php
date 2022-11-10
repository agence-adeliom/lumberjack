<?php

namespace App\Admin;

use Adeliom\Lumberjack\Admin\AbstractAdmin;
use Adeliom\Lumberjack\Admin\Fields\Buttons\ButtonField;
use Adeliom\Lumberjack\Admin\Fields\Medias\FileField;
use Adeliom\Lumberjack\Admin\Fields\Medias\ImageField;
use Adeliom\Lumberjack\Admin\Fields\Medias\MediaField;
use Adeliom\Lumberjack\Admin\Fields\Tabs\ContentTab;
use Adeliom\Lumberjack\Admin\Fields\Layout\LayoutField;
use Adeliom\Lumberjack\Admin\Fields\Tabs\LayoutTab;
use Adeliom\Lumberjack\Admin\Fields\Tabs\MediaTab;
use Adeliom\Lumberjack\Admin\Fields\Settings\SettingsField;
use Adeliom\Lumberjack\Admin\Fields\Tabs\SettingsTab;
use Adeliom\Lumberjack\Admin\Fields\Tabs\TabField;
use Adeliom\Lumberjack\Admin\Fields\Typography\HeadingField;
use Adeliom\Lumberjack\Admin\Fields\Typography\TextareaField;
use Adeliom\Lumberjack\Admin\Fields\Typography\WysiwygField;
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

        yield from ContentTab::make()->fields([
            HeadingField::make()->tag(),
            WysiwygField::make()->default(),
            ButtonField::make()->group()
        ]);

        yield from TabField::make("Description", "desc_tab")->fields([
            TextareaField::make(),
            FileField::make()->pdf(),
            ImageField::make()->ratio(150, 200)
        ]);

        yield from LayoutTab::make()->fields([
            LayoutField::darkMode(),
            LayoutField::margin([
                LayoutField::MARGIN_TOP_REMOVE,
                LayoutField::MARGIN_BOTTOM_REMOVE,
            ])
        ]);

        yield from MediaTab::make()->fields([
            MediaField::make()
        ]);

        yield from SettingsTab::make()->fields([
            SettingsField::anchor()
        ]);
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
