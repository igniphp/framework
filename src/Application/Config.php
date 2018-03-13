<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Container\ServiceLocator;
use Igni\Http;
use Igni\IO\File\Ini;
use Igni\IO\Path;
use Igni\Utils\ArrayUtil;

/**
 * Application's config container.
 * Treats dots as an operator for accessing nested values.
 * If constant name is put in curly braces as a value, it wil be replaced
 * to the constant value.
 *
 * @example:
 * // Example usage.
 * $config = new Config();
 * $config->set('some.key', true);
 * $some = $config->get('some'); // returns ['key' => true]
 *
 * @package Igni\Application
 */
class Config
{
    private static $prototypes = [
        'http' => [
            'class' => Http\Application::class,
            'type' => 'http',
            'container' => ServiceLocator::class,
        ],
        'mysql' => [
            'host' => 'localhost',
            'port' => 3306,
            'type' => 'mysql',
            'username' => '',
            'password' => '',
            'errors' => true,
            'persistence' => true,
        ],
        'sqlite' => [
            'type' => 'sqlite',
            'errors' => true,
        ],
        'mongo' => [
            'host' => 'localhost',
            'port' => 27017,
            'type' => 'mongodb',
            'username' => '',
            'password' => '',
            'errors' => true,
            'readConcern' => 'majority',
            'writeConcern' => 'majority',

        ],
        'pgsql' => [
            'host' => 'localhost',
            'port' => 5432,
            'type' => 'pgsql',
            'username' => '',
            'password' => '',
            'errors' => true,
            'persistence' => true,
        ],
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Checks if config key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return ArrayUtil::exists($this->config, $key);
    }

    /**
     * Gets value behind the key, or returns $default value if path does not exists.
     *
     * @param string $key
     * @param null $default
     * @return null|string|string[]
     */
    public function get(string $key, $default = null)
    {
        $result = ArrayUtil::get($this->config, $key);
        return $result === null ? $default : $this->fetchConstants($result);
    }

    /**
     * Merges one instance of Config class into current one and
     * returns current instance.
     *
     * @param Config $config
     * @return Config
     */
    public function merge(Config $config): Config
    {
        $this->config = array_merge_recursive($this->config, $config);

        return $this;
    }

    /**
     * Sets new value.
     *
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void
    {
        ArrayUtil::set($this->config, $key, $value);
    }

    /**
     * Returns array representation of the config.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * Factories config class from ini file.
     * Supports config autoload by glob search.
     *
     * @param string $path
     * @return Config
     */
    public static function fromIni(string $path): Config
    {
        $ini = new Ini($path, self::$prototypes);

        $config = new self($ini->parse());

        // Config autoloading.
        if ($config->has('config.autoload')) {
            $iniDirname = $ini->getFile()->getPath()->getDirectoryName();
            foreach ($config->get('config.autoload') as $glob) {
                $autoloadPath = Path::join($iniDirname, $glob);
                foreach (glob($autoloadPath, GLOB_BRACE) as $file) {
                    $autoloadedIni = new Ini($file, self::$prototypes);
                    $config->config = array_merge_recursive($config->config, $autoloadedIni->parse());
                }
            }
        }

        return $config;
    }

    private function fetchConstants($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return preg_replace_callback(
            '#\$\{([^{}]*)\}#',
            function($matches) {
                if (defined($matches[1])) {
                    return constant($matches[1]);
                }
                return $matches[0];
            },
            $value
        );
    }
}
