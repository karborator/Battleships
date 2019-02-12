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
            self::displayError($e);
        }
    }

    private static function displayError(\Throwable $error)
    {
        try {
            echo (View::factory(Config::getInstance()))->boot('error');
        var_dump($error->getMessage());exit;
        } catch (\Exception $e) {
            echo self::ERR_SYSTEM;
        }
    }
}