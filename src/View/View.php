<?php

namespace Battleships\View;

use Battleships\ConfigInterface;

class View implements ViewInterface
{
    const VIEW_TYPE = 'phtml';
    const VIEW_PATH = __DIR__ . '/template/';

    private $data;
    private $template;
    private $viewType;
    private $viewPath;

    public function __construct(string $viewType, string $viewPath)
    {
        $this->viewType = $viewType;
        $this->viewPath = $viewPath;
    }

    public static function factory(ConfigInterface $config)
    {
        $viewType = $config->get('viewType') ?? View::VIEW_TYPE;
        $viewPath = $config->get('viewPath') ?? View::VIEW_PATH;

        return new self($viewType, $viewPath);
    }

    public function boot(string $template, array $data = []): self
    {
        $this->data = $data;
        $this->template = $this->getTemplatePath($template);

        return $this;
    }

    public function __toString()
    {
        ob_start();
        extract($this->data, EXTR_OVERWRITE);

        include_once $this->template;

        return ob_get_clean();
    }

    private function getTemplatePath(string $templateName): string
    {
        $file = $this->viewPath . $templateName . '.' . $this->viewType;
        if (!file_exists($file) || !is_readable($file)) {
            throw ViewFailed::templateNotFound();
        }

        return $file;
    }
}