<?php

declare(strict_types=1);

namespace Adeliom\Lumberjack\Hooks\Models;

use Adeliom\Lumberjack\Hooks\Exceptions\ArgumentNotFoundException;
use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class Shortcode extends Model
{
    /**
     * @var string
     */
    protected string $handler = 'add_shortcode';

    /**
     * Shortcode constructor.
     *
     * @param string $tag
     * @throws ArgumentNotFoundException
     */
    public function __construct(string $tag)
    {
        $this->tag = $tag;
        $this->validateFields();
    }

    /**
     * Ordered indexed list of arguments expected by the trigger functions.
     *
     * @return array
     */
    protected function arguments(): array
    {
        return [$this->tag, $this->callable];
    }
}
