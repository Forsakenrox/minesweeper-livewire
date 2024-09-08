<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Minesweeper extends Component
{
    public $inputHeight = 12;
    public $inputWidth = 12;
    public $height;
    public $width;
    public $bombSpawnChance = 15;
    public $greed = [];
    public $isDebug = 0;
    public $mode = 'explore';

    public $gameStatus;

    public function rules()
    {
        return [
            'inputHeight' => ['integer', 'max:40', 'min:5'],
            'inputWidth' => ['integer', 'max:40', 'min:5']
        ];
    }

    public function mount()
    {
    }

    public function startGame()
    {
        $this->validate();
        $this->height = $this->inputHeight;
        $this->width = $this->inputWidth;
        $this->generateGreed();
        $this->spawnBombs();
        $this->calculateValuesOfCells();
        $this->gameStatus = 'running';
    }

    public function generateGreed()
    {
        $cellsCount = $this->height * $this->width;
        //Генерируем сетку
        $this->greed = [];
        for ($i = 0; $i < $cellsCount; $i++) {
            $cell = ['isShown' => false, 'isFlagged' => false, 'type' => '0'];
            array_push($this->greed, $cell);
        }
    }

    public function spawnBombs()
    {
        //Расставляем бомбы
        foreach ($this->greed as $key => $cell) {
            if (rand(1, 100) > 100 - $this->bombSpawnChance) {
                $this->greed[$key]['type'] = 'bomb';
            }
        }
    }

    public function calculateValuesOfCells()
    {
        foreach ($this->greed as $key => $cell) {

            $countsOfBombsNearCell = 0;
            //Скипаем ячейку если это бомба
            if ($cell['type'] == 'bomb') {
                continue;
            }

            //Сщитаем ячейку справа
            if ($key + 1 <= count($this->greed)) {
                if ((($key + 1) % $this->width) != 0) {
                    // if (is_int($key + 1 / $this->width) != 0) {
                    if ($this->greed[$key + 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }
            //Сщитаем ячейку слева
            if ($key - 1 >= 0) {
                if (!is_int($key / $this->width)) {
                    if ($this->greed[$key - 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }

            //Сщитаем ячейку сверху
            if ($key - $this->width >= 0) {
                if ($this->greed[$key - $this->width]['type'] == 'bomb') {
                    $countsOfBombsNearCell++;
                }
            }
            //Сщитаем ячейку снизу
            if ($key + $this->width < count($this->greed)) {
                if ($this->greed[$key + $this->width]['type'] == 'bomb') {
                    $countsOfBombsNearCell++;
                }
            }

            //Сщитаем ячейку справа внизу
            if ($key + $this->width + 1 < count($this->greed)) {
                if ((($key + 1) % $this->width) != 0) {
                    if ($this->greed[$key + $this->width + 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }
            //Сщитаем ячейку слева внизу
            if ($key + $this->width - 1 <= count($this->greed) - 1) {
                if (!is_int($key / $this->width)) {
                    if ($this->greed[$key + $this->width - 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }
            //Сщитаем ячейку слева вверху
            if ($key - $this->width - 1 >= 0) {
                if (!is_int($key / $this->width)) {
                    if ($this->greed[$key - $this->width - 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }
            //Сщитаем ячейку справа вверху
            if ($key - $this->height + 1 >= 0) {
                if ((($key + 1) % $this->width) != 0) {
                    if ($this->greed[$key - $this->height + 1]['type'] == 'bomb') {
                        $countsOfBombsNearCell++;
                    }
                }
            }
            $this->greed[$key]['type'] = $countsOfBombsNearCell;
        }
    }

    public function placeBomb($id)
    {
        $this->greed[$id]['type'] = 'bomb';
        $this->calculateValuesOfCells();
    }

    public function exploreCell($id)
    {
        // Log::debug($id);
        // if ($this->greed[$id]['type'] == 'flag') {
        //     continue;
        // }
        //блокирование функции если игра завершена проигрышем ил ипобедой
        if ($this->gameStatus != 'running') {
            return null;
        }
        //Если режим флагов
        if ($this->mode == 'explore') {
            //Если нажал на бомбу (((
            if ($this->greed[$id]['type'] == 'bomb') {
                foreach ($this->greed as $key => $cell) {
                    if ($cell['type'] == 'bomb') {
                        $this->greed[$key]['isShown'] = true;
                        $this->gameStatus = 'loose';
                    };
                }
            } elseif ($this->greed[$id]['type'] != '0') {
                $this->greed[$id]['isShown'] = true;
            } elseif ($this->greed[$id]['type'] == '0') {
                //Сщитаем ячейку справа
                $this->greed[$id]['isShown'] = true;

                if ($id + 1 <= count($this->greed)) {
                    if ((($id + 1) % $this->width) != 0) {
                        if ($this->greed[$id + 1]['type'] == '0' && $this->greed[$id + 1]['isShown'] == false) {
                            $this->exploreCell($id + 1);
                        } else {
                            $this->greed[$id + 1]['isShown'] = true;
                        }
                    }
                }
                //Сщитаем ячейку слева
                if ($id - 1 >= 0) {
                    if (!is_int($id / $this->width)) {
                        if ($this->greed[$id - 1]['type'] == '0' && $this->greed[$id - 1]['isShown'] == false) {
                            $this->exploreCell($id - 1);
                        } else {
                            $this->greed[$id - 1]['isShown'] = true;
                        }
                    }
                }
                //Сщитаем ячейку сверху
                if ($id - $this->width >= 0) {
                    if ($this->greed[$id - $this->width]['type'] == '0' && $this->greed[$id - $this->width]['isShown'] == false) {
                        $this->exploreCell($id - $this->width);
                    } else {
                        $this->greed[$id - $this->width]['isShown'] = true;
                    }
                }
                //Сщитаем ячейку снизу
                if ($id + $this->width < count($this->greed)) {
                    if ($this->greed[$id + $this->width]['type'] == '0' && $this->greed[$id + $this->width]['isShown'] == false) {
                        $this->exploreCell($id + $this->width);
                    } else {
                        $this->greed[$id + $this->width]['isShown'] = true;
                    }
                }
                //Сщитаем ячейку справа внизу
                if ($id + $this->width + 1 < count($this->greed)) {
                    if ((($id + 1) % $this->width) != 0) {
                        if ($this->greed[$id + $this->width + 1]['type'] == '0' && $this->greed[$id + $this->width + 1]['isShown'] == false) {
                            $this->exploreCell($id + $this->width + 1);
                        } else {
                            $this->greed[$id + $this->width + 1]['isShown'] = true;
                        }
                    }
                }
                //Сщитаем ячейку слева внизу
                if ($id + $this->width - 1 <= count($this->greed) - 1) {
                    // if ($id + $this->width - 1 <= count($this->greed) - 1) {
                    if (!is_int($id / $this->width)) {
                        if ($this->greed[$id + $this->width - 1]['type'] == '0' && $this->greed[$id + $this->width - 1]['isShown'] == false) {
                            $this->exploreCell($id + $this->width - 1);
                        } else {
                            $this->greed[$id + $this->width - 1]['isShown'] = true;
                        }
                    }
                }
                //Сщитаем ячейку слева вверху
                if ($id - $this->width - 1 >= 0) {
                    if (!is_int($id / $this->width)) {
                        if ($this->greed[$id - $this->width - 1]['type'] == '0' && $this->greed[$id - $this->width - 1]['isShown'] == false) {
                            $this->exploreCell($id - $this->width - 1);
                        } else {
                            $this->greed[$id - $this->width - 1]['isShown'] = true;
                        }
                    }
                }
                //Сщитаем ячейку справа вверху
                if ($id - $this->height + 1 >= 0) {
                    if ((($id + 1) % $this->width) != 0) {
                        if ($this->greed[$id - $this->height + 1]['type'] == '0' && $this->greed[$id - $this->height + 1]['isShown'] == false) {
                            $this->exploreCell($id - $this->height + 1);
                        } else {
                            $this->greed[$id - $this->height + 1]['isShown'] = true;
                        }
                    }
                }
            }
        } else {
            if ($this->greed[$id]['isShown'] == false) {
                if ($this->greed[$id]['isFlagged'] == true) {
                    $this->greed[$id]['isFlagged'] = false;
                } else {
                    $this->greed[$id]['isFlagged'] = true;
                }
            }
        }
        //Заканчиваем игру если все ячейки открыты
        $hiddenCellsCount = 0;
        foreach ($this->greed as $key => $cell) {
            if ($cell['isShown'] == false && $cell['type'] != 'bomb') {
                $hiddenCellsCount++;
            }
        }
        if ($hiddenCellsCount == 0) {
            $this->gameStatus = 'win';
            foreach ($this->greed as $key => $cell) {
                $this->greed[$key]['isShown'] = true;
            }
        }
    }

    public function render()
    {
        // dd($this->greed);
        return view('livewire.minesweeper');
    }
}
