<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures;

use Igni\Application\Controller;
use IgniTest\Fixtures\Boo;

class SampleController implements Controller
{
    private $boo;

    public function __construct(Boo $boo)
    {
        $this->boo = $boo;
    }

    public function __invoke()
    {
        return $this->boo->a->getA();
    }
}
