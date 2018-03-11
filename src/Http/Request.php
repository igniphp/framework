<?php declare(strict_types=1);

namespace Igni\Http;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\MessageTrait;
use Zend\Diactoros\RequestTrait;
use Zend\Diactoros\Uri;

class Request implements RequestInterface
{
    use MessageTrait, RequestTrait;

    public const METHOD_GET = 'GET';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_CONNECT = 'CONNECT';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_TRACE = 'TRACE';

    /** @var StreamInterface */
    private $stream;

    /**
     * @param null|string $uri URI for the request, if any.
     * @param string $method HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body Output body, if any.
     * @param array $headers Headers for the message, if any.
     * @throws InvalidArgumentException for any invalid value.
     */
    public function __construct(string $uri = null, string $method = self::METHOD_GET, $body = 'php://temp', array $headers = [])
    {
        $this->validateMethod($method);
        $this->validateUri($uri);
        $this->assertHeaders($headers);

        $this->method = $method;
        $this->uri = $uri ? new Uri($uri) : new Uri();
        $this->stream = Stream::create($body, 'wb+');

        list($this->headerNames, $this->headers) = $this->filterHeaders($headers);
        $headers['Host'] = $headers['Host'] ?? [$this->getHostFromUri()];
    }

    private function validateUri($uri)
    {
        if (!$uri instanceof UriInterface && !is_string($uri) && null !== $uri) {
            throw new InvalidArgumentException(
                'Invalid URI provided; must be null, a string, or a Psr\Http\Message\UriInterface instance'
            );
        }
    }
}
