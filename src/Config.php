<?php

namespace Battleships;

class Config implements ConfigInterface
{
    const INI_TYPE = 'ini';
    const PARSER = 'Parser';
    const CONFIG_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config';

    private static $instance;
    private $confDir;
    private $config = [];

    private function __construct(string $confDir)
    {
        $this->confDir = realpath($confDir);
        if (!is_readable($this->confDir) || !is_dir($this->confDir)) {
            throw new ConfigException('Config error, bad directory given.');
        }

        $this->parseConfig();
    }

    public static function getInstance(): ConfigInterface
    {
        return self::$instance ?? self::$instance = new static(self::CONFIG_DIR);
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }

    private function parseConfig()
    {
        $files = scandir($this->confDir);
        foreach ($files as $file) {
            $processed = $this->processParsing($this->confDir . DIRECTORY_SEPARATOR . $file);
            if ($processed && is_array($processed)) {
                $this->config = array_merge($this->config, $processed);
            }
        }
    }

    private function iniParser(string $file)
    {
        return parse_ini_file($file);
    }

    private function processParsing($file)
    {
        $pathExploded = explode('.', $file);
        $parserType = end($pathExploded);
        switch ($parserType) {
            case self::INI_TYPE;
                return $this->iniParser($file);
                break;
        }
    }
}