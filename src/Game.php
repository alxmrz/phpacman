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
        $level = new Level();

        $level->createFirst($this);
    }

    public function update(Event $event = null): void
    {
        $checkPairs = [];
        foreach ($this->gameObjects as $key => $gameObject) {
            $gameObject->update();

            if ($gameObject->needDestroy()) {
                unset($this->gameObjects[$key]);
            }

            if ($event instanceof KeyPressedEvent) {
                $gameObject->onButtonPressed($event, $this->gameObjects);
            }

            if (!$gameObject->isMovable()) {
                continue;
            }

            foreach ($this->gameObjects as $gameObject1) {
                if ($gameObject === $gameObject1) {
                    continue;
                }

                if (isset($checkPairs["{$gameObject->getId()}:{$gameObject1->getId()}"])
                    || isset($checkPairs["{$gameObject1->getId()}:{$gameObject->getId()}"])
                ) {
                    continue;
                }

                if ($gameObject->getCollision()->isCollidedWith($gameObject1->getCollision())) {
                    $gameObject->onCollision($gameObject1, $this->gameObjects);
                    $gameObject1->onCollision($gameObject, $this->gameObjects);
                }

                $checkPairs["{$gameObject->getId()}:{$gameObject1->getId()}"] = 1;
                $checkPairs["{$gameObject1->getId()}:{$gameObject->getId()}"] = 1;
            }
        }
    }

    public function draw(Renderer $renderer): void
    {
        $renderer->render($this->gameObjects);
    }

    public function addGameObject(GameObject $gameObject): void
    {
        $this->gameObjects[] = $gameObject;
    }
}