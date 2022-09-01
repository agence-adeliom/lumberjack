<?php

declare(strict_types=1);

namespace Adeliom\Lumberjack\Hooks\Tests\Data;

use Adeliom\Lumberjack\Hooks\HookRegistry;
use Adeliom\Lumberjack\Hooks\Models\Action;
use Adeliom\Lumberjack\Hooks\Models\Filter;
use Adeliom\Lumberjack\Hooks\Models\Shortcode;

class BarClass
{
    public function __construct(HookRegistry $hookRegistrar)
    {
        $hookRegistrar->bootstrap($this);
    }

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
     * @Shortcode(tag="cool_shortcode")
     */
    #[Shortcode(tag: 'cool_shortcode')]
    public static function bar()
    {
        return 'Bar';
    }

    /**
     * @Filter(tag="wp_title", priority= 99, accepted_args=2)
     */
    #[Filter(tag: 'wp_title', priority: 99, accepted_args: 2)]
    public static function baz()
    {
        return 'Baz';
    }
}
