<?php

namespace PHPacman;

use PHPacman\core\Collision;
use PHPacman\core\GameObject;
use PHPacman\core\GameObjects;
use PHPacman\core\Rectangle;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Food extends core\GameObject
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

        $this->width = $rect->getWidth();
        $this->height = $rect->getHeight();
        $this->color = $color;
    }

    public function onCollision(GameObject $gameObject, GameObjects $gameObjects): void
    {
        if ($gameObject instanceof Player) {
            $this->destroy();
        }
    }
}