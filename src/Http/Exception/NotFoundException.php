<?php declare(strict_types=1);

namespace Igni\Http\Exception;

class NotFoundException extends HttpException
{
    public static function notFound($uri, $method)
    {
        return new self("No resource found for $method $uri", 404);
    }
}
