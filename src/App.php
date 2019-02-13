<?php

namespace Battleships;

use Battleships\Controller\GridController;
use Battleships\View\View;

class App
{
    const ERR_SYSTEM = '<p>System out of reach !</p>';

    public static function start()
    {
        try {
            session_start(Config::getInstance()->get('session') ?? []);
            echo GridController::factory(Config::getInstance())->index(Request::getInstance());
        } catch (\Throwable $e) {
            self::log($e);
            self::displayError();
        }
    }

    private static function displayError()
    {
        try {
            echo (View::factory(Config::getInstance()))->boot('error');
        } catch (\Throwable $e) {
            self::log($e);
            echo self::ERR_SYSTEM;
        }
    }

    private static function log(\Throwable $e)
    {
        syslog(LOG_ERR, $e->getMessage());
    }
}