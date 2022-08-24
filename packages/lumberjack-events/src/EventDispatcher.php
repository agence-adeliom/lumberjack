<?php

namespace Adeliom\Lumberjack\Event;

use Blast\Facades\AbstractFacade;

class EventDispatcher extends AbstractFacade
{
    protected static function accessor(): string
    {
        return 'event_dispatcher';
    }
}
