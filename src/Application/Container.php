<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Container\ServiceLocator;
use Psr\Container\ContainerInterface;

final class Container extends ServiceLocator
{
    private $baseContainer;

    public function __construct(ContainerInterface $container = null)
    {
        $this->baseContainer = $container;
    }

    public function get($id, string $context = '')
    {
        if ($this->baseContainer && $this->baseContainer->has($id)) {
            return $this->baseContainer->get($id);
        }

        return parent::get($id, $context);
    }

    public function has($id, string $context = ''): bool
    {
        return ($this->baseContainer && $this->baseContainer->has($id)) || parent::has($id, $context);
    }
}
