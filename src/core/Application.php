<?php

namespace src\core;

use src\cli\Teams;
use src\exceptions\CliException;

class Application
{
      private Teams $teams;
      private GridGenerator $gridGenerator;

      public function __construct(array $params)
      {
            $this->teams = new Teams($params);
            $this->gridGenerator = new GridGenerator($this->teams->getCount());
      }

      public function run(): void
      {
            try {
                  $this->gridGenerator->drawGrid();
                  $this->gridGenerator->saveImage('tournament_grid.png');
            } catch (CliException $e) {
                  echo "Error: " . $e->getMessage();
            }
      }
}