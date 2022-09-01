<?php

namespace Adeliom\Lumberjack\Hooks\Tests;

use Adeliom\Lumberjack\Hooks\HookRegistry;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
    /**
     * @var HookRegistry
     */
    protected HookRegistry $hookRegistrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hookRegistrar = new HookRegistry();
    }

    protected function registerHook($class, $method, $buffer = true): ?string
    {
        if ($buffer) {
            ob_start();
        }

        $this->hookRegistrar->register([$class, $method]);

        if ($buffer) {
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }

        return null;
    }

    /**
     * @throws \JsonException
     */
    public function generateExpectedActionOrFilter($tag, $output, $function = 'add_action', $priority = 10, $acceptedArgs = 1): string
    {
        $expected = [
            'function' => $function,
            'tag' => $tag,
            'priority' => $priority,
            'accepted_args' => $acceptedArgs,
            'output' => $output,
        ];

        return json_encode($expected, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function generateExpectedShortcode($tag, $output): bool|string
    {
        $expected = [
            'function' => 'add_shortcode',
            'tag' => $tag,
            'output' => $output,
        ];

        return json_encode($expected, JSON_THROW_ON_ERROR);
    }
}
