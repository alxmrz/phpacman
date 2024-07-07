<?php

namespace PHPacman;

use PsyXEngine\Collision;
use PsyXEngine\GameObject;
use PsyXEngine\GameObjects;
use PsyXEngine\Rectangle;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Food extends GameObject
{
    public function __construct(SDLRect $rect, SDLColor $color)
    {
        $this->renderType = new Rectangle(
            $rect->getX()+($rect->getWidth() / 4),
            $rect->getY()+($rect->getHeight() / 4),
            $rect->getWidth() / 2,
            $rect->getHeight() / 2,
            $color
        );
        $this->collision = new Collision(
            $rect->getX(),
            $rect->getY(),
            $rect->getWidth(),
            $rect->getHeight()
        );
    }

    public function onCollision(GameObject $gameObject, GameObjects $gameObjects): void
    {
        if ($gameObject instanceof Player) {
            $this->destroy();
        }
    }
}