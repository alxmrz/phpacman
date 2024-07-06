<?php

namespace PHPacman\core;

use PHPacman\Game;
use PHPacman\ui\Message;
use PHPacman\ui\MessageBox;
use SDL2\KeyCodes;
use SDL2\LibSDL2;
use SDL2\LibSDL2Image;
use SDL2\LibSDL2Mixer;
use SDL2\LibSDL2TTF;
use SDL2\SDLColor;
use SDL2\SDLEvent;
use SDL2\SDLRect;

class Engine
{
    private const int LOOP_DELAY = 10;
    private string $windowTitle = 'Game app';
    private int $windowStartX = 50;
    private int $windowStartY = 50;
    private int $windowWidth = 100;
    private int $windowHeight = 100;

    private LibSDL2 $sdl;
    private Window $window;

    private bool $isRunning = true;
    private Renderer $renderer;
    private GameInterface $game;

    private ?Event $event = null;
    private LibSDL2TTF $ttf;
    private LibSDL2Image $imager;
    private LibSDL2Mixer $mixer;
    private Message $fpmMessage;
    private Message $loopTimeMessage;
    private Message $objectsCountMessage;

    private function init(): void
    {
        $this->sdl = LibSDL2::load();
        $this->ttf = LibSDL2TTF::load();
        $this->imager = LibSDL2Image::load();
        $this->mixer = LibSDL2Mixer::load();

        $this->window = new Window(
            $this->getWindowTitle(),
            $this->getWindowStartX(),
            $this->getWindowStartY(),
            $this->getWindowWidth(),
            $this->getWindowHeight()
        );

        $this->window->display();

        $this->renderer = $this->window->createRenderer($this->sdl, $this->ttf, $this->imager);
    }

    public function run(GameInterface $game): void
    {
        $this->game = $game;

        // TODO: in future it will be replaced with component injection
        if ($this->game instanceof Game) {
            //$this->game->setAudio($this->createAudio());
        }

        $this->init();

        $this->game->init();

        $this->game->addGameObject(new MessageBox(
            new SDLRect(550, 100, 335, 100),
            new SDLColor(0, 0, 0, 0)
        ));
        $this->game->addGameObject($this->fpmMessage = new Message(
            'FPS: ',
            new SDLRect(550, 100, 300, 100),
            new SDLColor(255, 0, 0, 0)
        ));
        $this->game->addGameObject($this->loopTimeMessage = new Message(
            'Loop time: ',
            new SDLRect(550, 250, 300, 50),
            new SDLColor(255, 0, 0, 0)
        ));

        $this->game->addGameObject($this->objectsCountMessage = new Message(
            'Objects in game: ',
            new SDLRect(550, 350, 300, 50),
            new SDLColor(255, 0, 0, 0)
        ));

        while ($this->isRunning) {
            $start = microtime(true);

            $this->handleEvents();
            $this->game->update($this->event);
            $this->game->draw($this->renderer);
            $this->reset();

            $end = microtime(true);
            $timeElapsed = round(($end - $start) * 1000);

            $this->delay(self::LOOP_DELAY);

            $frameRateTime = $timeElapsed + self::LOOP_DELAY;

            $this->fpmMessage->changeText('Framerate: ' . round(1000 / $frameRateTime, 0, PHP_ROUND_HALF_DOWN));
            $this->loopTimeMessage->changeText('Upd/draw time (ms): ' . $timeElapsed);
            $this->objectsCountMessage->changeText('Objects in game: ' . $this->game->countGameObjects());
        }

        $this->quit();
    }

    private function handleEvents(): void
    {
        $windowEvent = $this->sdl->createWindowEvent();
        while ($this->sdl->SDL_PollEvent($windowEvent)) {
            if (SDLEvent::SDL_QUIT === $windowEvent->type) {
                $this->isRunning = false;
                continue;
            }

            if (SDLEvent::SDL_MOUSEBUTTONDOWN === $windowEvent->type) {
                if ($windowEvent->button->button === KeyCodes::SDL_BUTTON_LEFT) {
                    $this->setEvent(
                        new ClickEvent(
                            [$windowEvent->button->x, $windowEvent->button->y],
                            true,
                            false
                        )
                    );
                } elseif ($windowEvent->button->button === KeyCodes::SDL_BUTTON_RIGHT) {
                    $this->setEvent(
                        new ClickEvent(
                            [$windowEvent->button->x, $windowEvent->button->y],
                            false,
                            true
                        )
                    );
                }
            }

            if (SDLEvent::SDL_KEYDOWN === $windowEvent->type) {
                $this->setEvent(new KeyPressedEvent($windowEvent->key->keysym->sym));
            }
        }
    }

    private function quit(): void
    {
        $this->window->close();
    }

    /**
     * @param int $ms
     * @return void
     */
    public function delay(int $ms): void
    {
        $this->sdl->SDL_Delay($ms);
    }

    private function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    private function reset(): void
    {
        $this->event = null;
    }

    private function createAudio(): Audio
    {
        return new Audio();
    }

    public function getWindowStartX(): int
    {
        return $this->windowStartX;
    }

    public function setWindowStartX(int $windowStartX): void
    {
        $this->windowStartX = $windowStartX;
    }

    public function getWindowStartY(): int
    {
        return $this->windowStartY;
    }

    public function setWindowStartY(int $windowStartY): void
    {
        $this->windowStartY = $windowStartY;
    }

    public function getWindowWidth(): int
    {
        return $this->windowWidth;
    }

    public function setWindowWidth(int $windowWidth): void
    {
        $this->windowWidth = $windowWidth;
    }

    public function getWindowHeight(): int
    {
        return $this->windowHeight;
    }

    public function setWindowHeight(int $windowHeight): void
    {
        $this->windowHeight = $windowHeight;
    }

    public function getWindowTitle(): string
    {
        return $this->windowTitle;
    }

    public function setWindowTitle(string $windowTitle): void
    {
        $this->windowTitle = $windowTitle;
    }
}