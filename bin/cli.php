<?php

require __DIR__ . '/../vendor/autoload.php';

use src\core\Application;

// TODO: Вынести все в класс Application
// TODO: Выводить картинки в отдельную папку (output)
// TODO: Выводить в одной картинке по 4 команды
// TODO: Подумать над нечетными значениями

// Получаем аргументы командной строки
$params = getopt("", ["teams:"]);

// Entry point
$app = new Application($params);
$app->run();