<?php

namespace Ecarus1\coldhot\Controller;

use function Ecarus1\cold_hot\View\showGame;
use function Ecarus1\cold_hot\View\showList;
use function Ecarus1\cold_hot\View\showReplay;
use function Ecarus1\cold_hot\View\help;

    // function startGame() {
    //     echo "Game started".PHP_EOL;
    //     showGame();
    // }
function key()
{
    $key = readline("Введите ключ: ");
    if ($key == "--new") {
        startGame();
    } elseif ($key == "--list") {
        showList();
    } elseif ($key == "--replay") {
        showReplay();
    } elseif ($key == "--help") {
        help();
    } else {
        echo "Не верный ключ.\n";
        key();
    }
}

function cold_hot($myNumber, $numbers)
{
    for ($i = 0; $i < 3; $i++) {
        if ($myNumber[$i] == $numbers[$i]) {
            echo "Горячо!\n";
        } elseif (
            $myNumber[$i] == $numbers[0] ||
            $myNumber[$i] == $numbers[1] ||
            $myNumber[$i] == $numbers[2]
        ) {
            echo "Тепло!\n";
        } else {
            echo "Холодно!\n";
        }
    }
}

function restart()
{
    $restart = readline("Хотите сыграть ещё?[Y/N]\n");
    if ($restart == "Y") {
        startGame();
    } else {
        exit;
    }
}

function startGame()
{
    showGame();
    $numbers = array();
    $myNumber = 0;
    $i = 0;
    for ($i = 0; $i < 3; $i++) {
        $numbers[$i] = random_int(1, 9);
    }
    repeated($myNumber, $numbers);
    restart();
}

function repeated($myNumber, $numbers)
{
    $myNumber = readline("Введите трехзначное число : ");
    if (strlen($myNumber) == 3) {
        if (!is_array($myNumber)) {
            $myNumber = str_split($myNumber);
        }
        if ($myNumber == $numbers) {
            echo "Вы выиграли!\n";
        } else {
            cold_hot($myNumber, $numbers);
            return repeated($myNumber, $numbers);
        }
    } else {
        echo "Вы ввели либо НЕ число, либо не 3-х значное число!\n";
        return repeated($myNumber, $numbers);
    }
}
