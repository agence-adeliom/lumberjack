<?php

namespace Adeliom\Lumberjack\Admin\Fields\Tabs;

use Adeliom\Lumberjack\Admin\Fields\Tabs\Traits\Fields;
use Extended\ACF\Fields\Tab;

class GlobalTab extends Tab
{
    use Fields;

    public const TAB = "global_tab";

    public static function make(string $label = "Global", string|null $name = self::TAB): static
    {
        return parent::make($label, $name);
    }
}