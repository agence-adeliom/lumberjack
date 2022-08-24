<?php

declare(strict_types=1);

namespace Adeliom\Lumberjack\Hooks\Tests\Data;

use Adeliom\Lumberjack\Hooks\Models\Action;
use Adeliom\Lumberjack\Hooks\Models\Filter;
use Adeliom\Lumberjack\Hooks\Models\Shortcode;

class FooClass
{
    /**
     * @Action(tag="init", priority=99, accepted_args=2)
     * @Filter(tag="wp_title")
     * @Shortcode(tag="my_shortcode")
     */
    #[Action(tag: 'init', priority: 99, accepted_args: 2)]
    #[Filter(tag: 'wp_title')]
    #[Shortcode(tag: 'my_shortcode')]
    public static function foo()
    {
        return 'Foo';
    }

    /**
     * @Filter(tag="wp_title")
     */
    #[Filter(tag: 'wp_title')]
    public static function foo1()
    {
        return 'New Title';
    }

    /**
     * @Filter(tag="wp_title", priority=99)
     */
    #[Filter(tag: 'wp_title', priority: 99)]
    public static function foo3()
    {
        return 'New Title';
    }

    /**
     * @Filter(tag="wp_title", priority=99, accepted_args=2)
     */
    #[Filter(tag: 'wp_title', priority: 99, accepted_args: 2)]
    public static function foo4()
    {
        return 'New Title';
    }

    /**
     * @Action(tag="init")
     */
    #[Action(tag: 'init')]
    public static function foo5()
    {
        return 'Foo';
    }
}
