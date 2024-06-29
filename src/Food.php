<?php

namespace Deminer;

use Deminer\core\Collision;
use Deminer\core\GameObject;
use Deminer\core\Rectangle;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Food extends core\GameObject
{
    public function __construct(SDLRect $rect, SDLColor $color)
    {
        $this->renderType = new Rectangle(
            $rect->getX()+5,
            $rect->getY()+5,
            15,
            15,
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

    public function onCollision(GameObject $gameObject): void
    {
        if ($gameObject instanceof Player) {
            $this->destroy();
        }
    }
}