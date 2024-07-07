<?php

namespace PHPacman;

use PsyXEngine\Audio;
use PsyXEngine\Collision;
use PsyXEngine\GameObject;
use PsyXEngine\GameObjects;
use PsyXEngine\Image;
use PsyXEngine\KeyPressedEvent;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Player extends GameObject
{
    private const string SPRITES = __DIR__ . '/../resources/pacman-sprites.png';
    private const string EAT_SOUND = __DIR__ . '/../resources/wakawaka.mp3';

    private const int DIRECTION_NONE = 0;
    private const int DIRECTION_UP = 1;
    private const int DIRECTION_DOWN = 2;
    private const int DIRECTION_LEFT = 3;
    private const int DIRECTION_RIGHT = 4;
    private const int DIRECTION_MOVE_STEP = 5;
    private int $direction = self::DIRECTION_NONE;
    private int $width;
    private SDLColor $color;
    private int $height;
    private int $score = 0;
    private int $oldDirection = self::DIRECTION_NONE;
    private ?Collision $expectedPosition = null;
    private int $nextDirection = self::DIRECTION_NONE;

    private array $frames = [
        self::DIRECTION_UP => [
            [103, 168, true],
            [120, 151, true],
            [120, 134, true],
        ],
        self::DIRECTION_DOWN => [
            [103, 168, false],
            [120, 151, false],
            [120, 134, false],
        ],
        self::DIRECTION_LEFT => [
            [103, 168, true],
            [103, 151, true],
            [103, 134, true],
        ],
        self::DIRECTION_RIGHT => [
            [103, 168, false],
            [103, 151, false],
            [103, 134, false],
        ],
        self::DIRECTION_NONE => [
            [103, 168, false],
            [103, 151, false],
            [103, 151, false],
        ],
    ];
    private int $currentFrame = 0;
    private Audio $audio;


    public function getCurrentFrame(): array
    {
        return $this->frames[$this->direction][$this->currentFrame];
    }

    public function __construct(SDLRect $rect, SDLColor $color)
    {
        $this->width = $rect->getWidth();
        $this->height = $rect->getHeight();
        $this->color = $color;
        $this->audio = new Audio();
        $this->renderType = $this->createRenderTypeImage($rect->getX(), $rect->getY());

        $this->collision = new Collision(
            $rect->getX()+ 10,
            $rect->getY() + 10,
            $rect->getWidth() - 10,
            $rect->getHeight() - 10
        );
    }

    public function isMovable(): bool
    {
        return true;
    }

    public function onCollision(GameObject $gameObject, GameObjects $gameObjects): void
    {
        if ($this->direction !== self::DIRECTION_NONE) {
            if ($gameObject instanceof Wall) {
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

        }

        if ($gameObject instanceof Food) {
            if (!$this->audio->isChannelPlaying()) {
                $this->audio->playChunk(self::EAT_SOUND);
            }

            $this->score += 1;
        }
    }

    public function onButtonPressed(KeyPressedEvent $event, GameObjects $gameObjects): void
    {
        $this->oldDirection = $this->direction;

        if ($event->isUpArrowKeyPressed()) {
            $this->setDirectionUp();
        } elseif ($event->isDownArrowKeyPressed()) {
            $this->setDirectionDown();
        } elseif ($event->isLeftArrowKeyPressed()) {
            $this->setDirectionLeft();
        } elseif ($event->isRightArrowKeyPressed()) {
            $this->setDirectionRight();
        }

        if ($this->oldDirection !== self::DIRECTION_NONE && !$this->canMoveAsBefore($gameObjects)) {
            if ($this->expectedPosition) {
                $this->nextDirection = $this->direction;
            }
            $this->direction = $this->oldDirection;
        }

    }

    public function update(): void
    {
        $this->currentFrame++;
        if ($this->currentFrame > 2) {
            $this->currentFrame = 0;
        }

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

        if ($this->expectedPosition
            && ($this->renderType->x === $this->expectedPosition->x || $this->renderType->y === $this->expectedPosition->y)
        ) {
            $this->expectedPosition = null;
            $this->direction = $this->nextDirection;
            $this->nextDirection = self::DIRECTION_NONE;

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
        $this->renderType = $this->createRenderTypeImage($x, $y);

        $this->collision = new Collision(
            $x,
            $y,
            $this->width,
            $this->height,
        );
    }

    private function createRenderTypeImage(int $x, int $y): Image
    {
        $image = new Image(
            self::SPRITES,
            new SDLRect(
                $x,
                $y,
                $this->width,
                $this->height,
            ),
            new SDLRect(
                $this->getCurrentFrame()[0],
                $this->getCurrentFrame()[1],
                16,
                16
            ),
            $this->getCurrentFrame()[2] ? 180:0
        );

        return $image;
    }

    /**
     * @param GameObjects $gameObjects
     * @return bool
     */
    private function canMoveAsBefore(GameObjects $gameObjects): bool
    {
        $newPosition = new Collision(
            $this->collision->x,
            $this->collision->y,
            $this->collision->width,
            $this->collision->height
        );

        switch ($this->direction) {
            case self::DIRECTION_UP:
                $newPosition->y -= $this->height + 1;
                break;
            case self::DIRECTION_DOWN:
                $newPosition->y += $this->height+ 1;
                break;
            case self::DIRECTION_LEFT:
                $newPosition->x -= $this->width+ 1;
                break;
            case self::DIRECTION_RIGHT:
                $newPosition->x += $this->width+ 1;
                break;
        }

        $result = true;

        foreach ($gameObjects as $gameObject) {
            if (!$gameObject->isCollidable() || !$newPosition->isCollidedWith($gameObject->getCollision())) {
                continue;
            }

            if ($gameObject instanceof Wall) {
                $result = false;
            }

            if ($gameObject instanceof Road
                && ($this->renderType->x !== $gameObject->collision->x && $this->renderType->y !== $gameObject->collision->y)
            ) {
                $this->expectedPosition = $gameObject->getCollision();
            }
        }

        return $result;
    }
}