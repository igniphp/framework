<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Exception\InvalidArgumentException;
use Zend\Diactoros\Stream as BaseStream;
use Psr\Http\Message\StreamInterface;

/**
 * @package Igni\Http
 */
final class Stream extends BaseStream
{
    public static function fromString(string $content): StreamInterface
    {
        $stream = new self('php://temp', 'wb+');
        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    public static function fromFile(string $path, string $mode = 'r'): StreamInterface
    {
        if ($path === 'php://input') {
            return new self($path, 'r');
        }

        return new self($path, $mode);
    }

    public static function create($stream, string $mode = 'r'): StreamInterface
    {
        if (!is_string($stream) && !is_resource($stream) && !$stream instanceof StreamInterface) {
            throw new InvalidArgumentException(
                'Stream must be a string stream resource identifier, '
                . 'an actual stream resource, '
                . 'or a Psr\Http\Message\StreamInterface implementation'
            );
        }

        if (is_string($stream) && (empty($stream) || 0 !== strpos('php://', $stream))) {
            return self::fromString($stream);
        }

        return $stream instanceof StreamInterface
            ? $stream
            : self::fromFile($stream, $mode);
    }
}
