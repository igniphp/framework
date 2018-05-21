<?php declare(strict_types=1);

namespace Igni\Application\Exception;

use Igni\Application\Config;

class ConfigException extends ApplicationException
{
    public static function forInvalidValue($key, $expectedType, $givenType): ConfigException
    {
        return new self("Invalid configuration at $key, expected $expectedType, given $givenType");
    }

    public static function forUnreadableDirectory(string $dir)
    {
        return new self("Could not read configuration dir $dir, make sure that dir exists and process has read access to it.");
    }

    public static function forUnsupportedFileType($path): ConfigException
    {
        return new self(sprintf(
            '%s does not does not recognize the file: %s',
            Config::class,
            $path
        ));
    }

    public static function forInvalidSource($source): ConfigException
    {
        return new self(sprintf(
            '%s::from($source) expects argument to be either string or array %s passed',
            Config::class,
            gettype($source)
        ));
    }
}
