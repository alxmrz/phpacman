<?php

namespace Deminer;

use Closure;
use Deminer\core\Audio;
use Deminer\core\ClickEvent;
use Deminer\core\Collision;
use Deminer\core\Event;
use Deminer\core\GameInterface;
use Deminer\core\GameObject;
use Deminer\core\KeyPressedEvent;
use Deminer\core\Renderer;
use Deminer\ui\Button;
use Deminer\ui\Element;
use Deminer\ui\Message;
use Deminer\ui\MessageBox;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Game implements GameInterface
{
    /**
     * @var GameObject[]
     */
    private array $gameObjects = [];

    public function init(): void
    {
        $this->gameObjects[] = new Player(
            new SDLRect(150, 150, 25, 25),
            new SDLColor(255, 0, 0, 0)
        );

        $this->gameObjects[] = new Wall(
            new SDLRect(250, 150, 550, 50),
            new SDLColor(0, 0, 0, 0)
        );

        for ($i = 0; $i < 10; ++$i) {
            $this->gameObjects[] = new Food(
                new SDLRect(150 + ($i * 25) + 5, 350, 25, 25),
                new SDLColor(0, 255, 0, 0)
            );
        }
    }

    public function update(Event $event = null): void
    {
        foreach ($this->gameObjects as $key => $gameObject) {
            if ($gameObject->needDestroy()) {
                unset($this->gameObjects[$key]);
            }

            if ($gameObject instanceof Wall) {
                continue;
            }

            if ($event instanceof KeyPressedEvent) {
                $gameObject->onButtonPressed($event);
            }

            foreach ($this->gameObjects as $gameObject1) {
                if ($gameObject === $gameObject1) {
                    continue;
                }

                if ($gameObject->getCollision()->isCollidedWith($gameObject1->getCollision())
                || $gameObject1->getCollision()->isCollidedWith($gameObject->getCollision())
                ) {
                    $gameObject->onCollision($gameObject1);
                }
            }

            $gameObject->update();
        }
    }

    public function draw(Renderer $renderer): void
    {
        $renderer->render($this->gameObjects);
    }
}