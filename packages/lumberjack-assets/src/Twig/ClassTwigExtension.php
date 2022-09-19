<?php

namespace Adeliom\Lumberjack\Assets\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class ClassTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('concat', [$this, 'concat'])
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('mergeClass', [$this, 'mergeClass'])
        ];
    }

    public static function mergeClass(): string
    {

        $classes = "";

        $args = func_get_args();

        $className = $args[0];

        if (is_string($className)) {
            return self::getClasses($args, $classes);
        }

        $options = $args[1] ?? [];

        foreach ($options as $key => $value) {
            if (isset($className[$key]) && isset($className[$key][$value])) {
                $classes .= " " . $className[$key][$value];
            }
        }

        if (isset($className["default"])) {
            foreach ($className["default"] as $key => $value) {
                if (empty($options[$key]) && $options[$key] !== 0) {
                    $classes .= " " . $className[$key][$value];
                }
            }
        }

        if (isset($className["base"])) {
            $classes .= " " . $className["base"];
        }

        return self::getClasses(array_slice($args, 2), $classes);
    }

    public static function concat(): string
    {
        return self::getClasses(func_get_args());
    }

    private static function getClasses(array $args, string $classes = ""): string
    {
        $classes .= " " . join(" ", $args);
        return $classes;
    }
}
