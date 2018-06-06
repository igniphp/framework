<?php
chdir(__DIR__);

// Swoole ssl support.
if (!defined('SWOOLE_SSL')) {
    define('SWOOLE_SSL', 1);
}

if (!defined('IS_TEST')) {
    define('IS_TEST', true);
}

require_once __DIR__ . '/../vendor/autoload.php';
