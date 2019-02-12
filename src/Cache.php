<?php

namespace Battleships;


use Psr\SimpleCache\CacheInterface;

class Cache implements CacheInterface
{
    private static $instance;

    const CACHE_APP = 'app';

    protected function __construct()
    {
        $_SESSION[self::CACHE_APP] = $_SESSION[self::CACHE_APP] ?? null;
    }

    public static function getInstance(): CacheInterface
    {
        return self::$instance ?? self::$instance = new static();
    }

    public function get($key, $default = null)
    {
        return $_SESSION[self::CACHE_APP][$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        if ($ttl) {
            throw CacheFailed::argumentNotImplemented('ttl');
        }

        $_SESSION[self::CACHE_APP][$key] = $value;
    }

    public function delete($key)
    {
        unset($_SESSION[self::CACHE_APP][$key]);
    }

    public function clear()
    {
        session_destroy();
    }

    public function getMultiple($keys, $default = null)
    {
        throw CacheFailed::methodNotImplemented();
    }

    public function setMultiple($values, $ttl = null)
    {
        throw CacheFailed::methodNotImplemented();
    }

    public function deleteMultiple($keys)
    {
        throw CacheFailed::methodNotImplemented();
    }

    public function has($key)
    {
        return isset($_SESSION[self::CACHE_APP][$key]);
    }
}