<?php

namespace PHPacman;

use SDL2\SDLColor;
use SDL2\SDLRect;

class Level
{
    private string $level1 = "
*****************|
*SSSSSSS*SSSSSSS*|
*S*S***S*S***S*S*|
*SSSSSSSSSSSSSSS*|
*S*S**S***S**S*S*|
*SSS*SSSSSSS*SSS*|
***S*S**S**S*S***|
SSSSSS*SSS*SSSSSS|
***S*S*****S*S***|
*SSS*SSSSSSS*SSS*|
*S*S**S***S**S*S*|
*SSSSSSSPSSSSSSS*|
*S*S***S*S***S*S*|
*SSSSSSS*SSSSSSS*|
*****************|
";
    public function createFirst(Game $game): void
    {
        $rows = explode("|", $this->level1);
        $y = 0;
        $width = 30;


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
                        $game->addGameObject(
                            new Road(
                                new SDLRect($x + $i * $width, $y, $width, $width),
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