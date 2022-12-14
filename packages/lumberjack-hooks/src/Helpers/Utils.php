<?php

declare(strict_types=1);

namespace Adeliom\Lumberjack\Hooks\Helpers;

/**
 * Safely retrieve key from array.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed|null
 */
function get(array $array, string $key, $default = null)
{
    if (!array_key_exists($key, $array)) {
        return $default;
    }

    return $array[$key];
}

/**
 * Get the class "basename" of the given object / class.
 *
 * @param object|string $class
 * @return string
 */
function class_basename($class): string
{
    $class = is_object($class) ? get_class($class) : $class;

    return basename(str_replace('\\', '/', $class));
}

/**
 * Prepares a function name for display.
 *
 * @param array|string $function
 *
 * @return string
 */
function normalize_function($function): string
{
    if (is_array($function) && count($function) === 2) {
        [$class, $method] = $function;
        return "{$class}::$method";
    }

    return (string)$function;
}
