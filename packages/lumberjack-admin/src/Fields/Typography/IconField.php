<?php

namespace Adeliom\Lumberjack\Admin\Fields\Typography;

use Extended\ACF\Fields\Field;

class IconField extends Field
{
    protected string|null $type = 'font-awesome';

    private const ICON = "icon";

    /**
     * Icône
     */
    public static function make(string $label = "Icône", ?string $name = null): static
    {
        return parent::make($label, $name ?? self::ICON);
    }
}