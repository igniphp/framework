<?php declare(strict_types=1);

namespace Igni\Application\Controller;

/**
 * Interface for controller container. It allows for registering application's controllers.
 * Each application must compose an instance of ControllerAggregate, so ControllerProvider
 * can be used.
 * 
 * @package Igni\Application\Controller
 */
interface ControllerAggregate
{
    public function add($controller): void;
}
