<?php

namespace src\core;

use src\cli\Teams;
use src\exceptions\CliException;

class Application
{
    private array $params;
    private Teams $teams;
    private GridGenerator $gridGenerator;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function run(): void
    {
        try {
            $this->teams = new Teams($this->params);
        } catch (CliException $e) {
            echo "Error: " . $e->getMessage();
        }
        $this->gridGenerator = new GridGenerator($this->teams->getCount());
        $this->gridGenerator->drawGrid();
        $this->gridGenerator->saveImage('tournament_grid.png');
    }
}