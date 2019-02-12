<?php

namespace Battleships;

class Request
{
    const HTTP_POST = 'POST';

    private $post;
    private $server;

    private function __construct(
        array $post,
        array $server
    ) {
        $this->post = $post;
        $this->server = $server;
    }

    public static function getInstance()
    {
        return new self($_POST, $_SERVER);
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getServer(): array
    {
        return $this->server;
    }

    public function isPost()
    {
        return self::HTTP_POST === $this->getServer()['REQUEST_METHOD'];
    }
}