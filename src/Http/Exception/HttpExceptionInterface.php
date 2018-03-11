<?php declare(strict_types=1);

namespace Igni\Http\Exception;

interface HttpExceptionInterface
{
    public function getHttpStatusCode();
    public function getHttpBody();
}
