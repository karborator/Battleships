<?php

namespace Battleships\Controller;

use Battleships\Config;
use Battleships\Model\Grid;
use Battleships\Model\GridModelInterface;
use Battleships\Request;
use Battleships\View\View;
use Battleships\View\ViewInterface;

class GridController implements ControllerInterface
{
    private $gridModel;
    private $view;

    public function __construct(GridModelInterface $gridModel, ViewInterface $view)
    {
        $this->gridModel = $gridModel;
        $this->view = $view;
    }

    public static function factory(Config $config): self
    {
        return new GridController(
            Grid::factory(),
            View::factory($config)
        );
    }

    public function index(Request $request): ViewInterface
    {
        if ($request->isPost()) {
            $this->gridModel->shoot($this->getCoordinates($request->getPost()));
        }

        $viewData = $this->gridModel->getSettings();
        $viewData += $this->gridModel->getValidationMessages();
        $viewData += $this->gridModel->getShootMessages();
        $viewData += [
            'coordinatesMiss' => $this->gridModel->getCoordinatesMiss(),
            'coordinatesHit' => $this->gridModel->getCoordinatesHit(),
            'gridData' => $this->gridModel->generateGrid(),
            'playAgain' => $this->gridModel->getPlayAgain(),
        ];

        return $this->view->boot('grid', $viewData);
    }

    private function getCoordinates(array $post): string
    {
        $coordinates = $post['coord'] ?? null;
        if (!$coordinates) {
            throw new \Exception('Required param coord missing');
        }
        return strtoupper($coordinates);
    }
}