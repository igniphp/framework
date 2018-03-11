<?php declare(strict_types=1);

namespace Igni\Application\Controller;

interface ControllerAggregate
{
    public function add($controller): void;
}
