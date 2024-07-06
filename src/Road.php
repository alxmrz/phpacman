<?php

namespace PHPacman;

use PHPacman\core\Collision;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Road extends core\GameObject
{
    public function __construct(SDLRect $rect)
    {
        $this->collision = new Collision(
            $rect->getX(),
            $rect->getY(),
            $rect->getWidth(),
            $rect->getHeight()
        );
    }

    public function isDisplayable(): bool
    {
        return false;
    }
}