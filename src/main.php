<?php

use Deminer\core\Engine;
use Deminer\Game;

require_once __DIR__ . '/../vendor/autoload.php';

$engine = new Engine();

$engine->setWindowTitle('PHPacman');
$engine->setWindowWidth(900);
$engine->setWindowHeight(600);

$engine->run(new Game());
