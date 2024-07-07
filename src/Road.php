<?php

namespace PHPacman;

use PsyXEngine\Collision;
use PsyXEngine\GameObject;
use SDL2\SDLRect;

class Road extends GameObject
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
