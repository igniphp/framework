<?php declare(strict_types=1);

namespace Igni\Http\Exception;

interface HttpException
{
    public function getHttpStatusCode(): int;
    public function getHttpBody(): string;
}
