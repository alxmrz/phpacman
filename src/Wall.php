<?php

namespace PHPacman;

use PsyXEngine\Collision;
use PsyXEngine\GameObject;
use PsyXEngine\Rectangle;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Wall extends GameObject
{
    public function __construct(SDLRect $rect, SDLColor $color)
    {
        $this->renderType = new Rectangle(
            $rect->getX(),
            $rect->getY(),
            $rect->getWidth(),
            $rect->getHeight(),
            $color
        );
        $this->collision = new Collision(
            $rect->getX(),
            $rect->getY(),
            $rect->getWidth(),
            $rect->getHeight()
        );
    }
}