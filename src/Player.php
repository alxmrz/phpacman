<?php

namespace Deminer;

use Deminer\core\Collision;
use Deminer\core\GameObject;
use Deminer\core\KeyPressedEvent;
use Deminer\core\Rectangle;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Player extends GameObject
{

    private const DIRECTION_NONE = 0;
    private const DIRECTION_UP = 1;
    private const DIRECTION_DOWN = 2;
    private const DIRECTION_LEFT = 3;
    private const DIRECTION_RIGHT = 4;
    private const DIRECTION_MOVE_STEP = 10;
    private int $direction = self::DIRECTION_NONE;
    private int $width;
    private SDLColor $color;
    private int $height;
    private int $score = 0;

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

        $this->width = $rect->getWidth();
        $this->height = $rect->getHeight();
        $this->color = $color;
    }

    public function isMovable(): bool
    {
        return true;
    }

    public function onCollision(GameObject $gameObject): void
    {
        if ($this->direction !== self::DIRECTION_NONE && $gameObject instanceof Wall) {
            switch ($this->direction) {
                case self::DIRECTION_UP:
                    $this->moveDown();
                    break;
                case self::DIRECTION_DOWN:
                    $this->moveUp();
                    break;
                case self::DIRECTION_LEFT:
                    $this->moveRight();
                    break;
                case self::DIRECTION_RIGHT:
                    $this->moveLeft();
                    break;
            }

            $this->direction = self::DIRECTION_NONE;
        }

        if ($gameObject instanceof Food) {
            $this->score += 1;
        }
    }

    public function onButtonPressed(KeyPressedEvent $event): void
    {
        if ($event->isUpArrowKeyPressed()) {
            $this->setDirectionUp();
        } elseif ($event->isDownArrowKeyPressed()) {
            $this->setDirectionDown();
        } elseif ($event->isLeftArrowKeyPressed()) {
            $this->setDirectionLeft();
        } elseif ($event->isRightArrowKeyPressed()) {
            $this->setDirectionRight();
        }
    }

    public function update(): void
    {
        switch ($this->direction) {
            case self::DIRECTION_UP:
                $this->moveUp();
                break;
            case self::DIRECTION_DOWN:
                $this->moveDown();
                break;
            case self::DIRECTION_LEFT:
                $this->moveLeft();
                break;
            case self::DIRECTION_RIGHT:
                $this->moveRight();
                break;
        }
    }

    private function setDirectionUp(): void
    {
        $this->direction = self::DIRECTION_UP;
    }

    private function setDirectionDown(): void
    {
        $this->direction = self::DIRECTION_DOWN;
    }

    private function setDirectionLeft(): void
    {
        $this->direction = self::DIRECTION_LEFT;
    }

    private function setDirectionRight(): void
    {
        $this->direction = self::DIRECTION_RIGHT;
    }

    private function moveUp(): void
    {
        $this->recreatePosition($this->getRenderType()->x, $this->getRenderType()->y - self::DIRECTION_MOVE_STEP);
    }

    private function moveDown(): void
    {
        $this->recreatePosition($this->getRenderType()->x, $this->getRenderType()->y + self::DIRECTION_MOVE_STEP);
    }

    private function moveLeft(): void
    {
        $this->recreatePosition($this->getRenderType()->x - self::DIRECTION_MOVE_STEP, $this->getRenderType()->y);
    }

    private function moveRight(): void
    {
        $this->recreatePosition($this->getRenderType()->x + self::DIRECTION_MOVE_STEP, $this->getRenderType()->y);
    }

    private function recreatePosition(int $x, int $y): void
    {
        $this->renderType = new Rectangle(
            $x,
            $y,
            $this->width,
            $this->height,
            $this->color
        );

        $this->collision = new Collision(
            $x,
            $y,
            $this->width,
            $this->height,
        );
    }
}