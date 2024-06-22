<?php

require __DIR__ . '/../vendor/autoload.php';

use src\cli\Teams;
use src\core\GridGenerator;
use src\exceptions\CliException;

// TODO: Вынести все в класс Application
// TODO: Выводить картинки в отдельную папку (output)
// TODO: Выводить в одной картинке по 4 команды
// TODO: Подумать над нечетными значениями

try {
      // Парсим аргументы командной строки
      $params = getopt("", ["teams:"]);
      // Создаем новый класс Teams, он проверяет переданные аргументы
      $teamsClass = new Teams($params);
      // $teamsCount(int) - Количество команд
      $teamsCount = $teamsClass->getCount();
      // GNU Data class
      $gridGenerator = new GridGenerator($teamsCount);
      $gridGenerator->drawGrid();
      $gridGenerator->saveImage('tournament_grid.png');
} catch (CliException $e) {
      echo "Error: " . $e->getMessage();
}