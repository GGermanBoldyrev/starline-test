<?php

require __DIR__ . '/../vendor/autoload.php';

use src\cli\Teams;
use src\exceptions\CliException;

// TODO: Вынести все в класс Application

try {
      // Парсим аргументы командной строки
      $params = getopt("", ["teams:"]);
      // Создаем новый класс Teams, он проверяет переданные аргументы
      $teamsClass = new Teams($params);
      // $teamsCount(int) - Количество команд
      $teamsCount = $teamsClass->execute();

} catch (CliException $e) {
      echo "Error: " . $e->getMessage();
}