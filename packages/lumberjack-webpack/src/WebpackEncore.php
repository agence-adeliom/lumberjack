<?php

namespace Adeliom\Lumberjack\Webpack;

use Blast\Facades\AbstractFacade;

/**
 * @method static array jsFiles(string $entrypoint)
 * @method static array cssFiles(string $entrypoint)
 * @method static void scriptTags(string $entrypoint)
 * @method static void linkTags(string $entrypoint)
 * @method static string asset(string $ressource)
 * @method static array enqueue(string $name, string $entrypoint, array $config)
 */
class WebpackEncore extends AbstractFacade
{
    public static function accessor(): string
    {
        return 'webpack';
    }
}
