<?php

namespace PHPacman;

use PsyXEngine\Event;
use PsyXEngine\GameInterface;
use PsyXEngine\GameObjects;

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
