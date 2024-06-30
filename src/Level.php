<?php

namespace Deminer;

use Deminer\core\GameObject;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Level
{
    private string $level1 = "
*********************|
***SSSSSSSSSSSSSSS***|
***S**S***S**S**S****|
***S**S***S**S**S****|
***S**S***S**S**S****|
***S**S***S**S**S****|
***SSSSSSSPSSSSSS****|
*********************|
";
    public function createFirst(Game $game): void
    {
        $rows = explode("|", $this->level1);
        $y = 0;
        $width = 40;


        foreach ($rows as $key => $row) {
            $x = 0;

            for ($i = 0; $i < strlen($row); $i++) {
                switch ($row[$i]) {
                    case "*":
                       $game->addGameObject(
                           new Wall(
                               new SDLRect($x + $i * $width, $y, $width, $width),
                               new SDLColor(0, 0, 0, 0)
                           )
                       );
                       break;
                    case "S":
                        $game->addGameObject(
                            new Food(
                                new SDLRect($x + $i * $width, $y, $width, $width),
                                new SDLColor(0, 255, 0, 0)
                            )
                        );
                        break;
                    case "P":
                        $game->addGameObject(
                            new Player(
                                new SDLRect($x + $i * $width, $y, $width, $width),
                                new SDLColor(255, 0, 0, 0)
                            )
                        );
                        break;
                }
            }
            $y += $width;
        }
    }
}