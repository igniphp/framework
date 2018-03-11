<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Container\ServiceLocator;
use Igni\Http;
use Igni\IO\File\Ini;
use Igni\IO\Path;
use Igni\Utils\ArrayUtil;

class Config
{
    private const PROTOTYPES = [
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

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function has(string $key): bool
    {
        return ArrayUtil::exists($this->config, $key);
    }

    public function get(string $key, $default = null)
    {
        $result = ArrayUtil::get($this->config, $key);
        return $result === null ? $default : $this->fetchConstants($result);
    }

    public function merge(Config $config): Config
    {
        $this->config = array_merge_recursive($this->config, $config);

        return $this;
    }

    public function set(string $key, $value): void
    {
        ArrayUtil::set($this->config, $key, $value);
    }

    public function toArray(): array
    {
        return $this->config;
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

    public static function fromIni(string $path): Config
    {
        $ini = new Ini($path, self::PROTOTYPES);

        $config = new self($ini->parse());

        // Config autoloading.
        if ($config->has('config.autoload')) {
            $iniDirname = $ini->getFile()->getPath()->getDirectoryName();
            foreach ($config->get('config.autoload') as $glob) {
                $autoloadPath = Path::join($iniDirname, $glob);
                foreach (glob($autoloadPath, GLOB_BRACE) as $file) {
                    $autoloadedIni = new Ini($file, self::PROTOTYPES);
                    $config->config = array_merge_recursive($config->config, $autoloadedIni->parse());
                }
            }
        }

        return $config;
    }
}
