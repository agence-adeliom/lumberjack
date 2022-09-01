<?php

declare(strict_types=1);

namespace Adeliom\Lumberjack\Hooks\Helpers;

use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader('class_exists');
