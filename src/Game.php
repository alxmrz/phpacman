<?php

namespace PHPacman;

use PHPacman\core\Event;
use PHPacman\core\GameInterface;
use PHPacman\core\GameObjects;

class Game implements GameInterface
{
    public function init(GameObjects $gameObjects): void
    {
        $level = new Level();

        $level->createFirst($gameObjects);
    }

    public function update(Event $event = null): void
    {
    }
}
