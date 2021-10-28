<?php

namespace Ecarus1\coldhot\Controller;

//use SQLite3;

use function Ecarus1\coldhot\View\showGame;
use function Ecarus1\coldhot\View\help;
use function Ecarus1\coldhot\Model\insertDB;
use function Ecarus1\coldhot\Model\showList;
use function Ecarus1\coldhot\Model\showReplay;
use function Ecarus1\coldhot\Model\insertReplay;
use function Ecarus1\coldhot\Model\updateDB;

    // function startGame() {
    //     echo "Game started".PHP_EOL;
    //     showGame();
    // }
function key($key, $id)
{
    if ($key === "--new" || $key === "-n") {
        startGame();
    } elseif ($key === "--list" || $key === "-l") {
        showList();
    } elseif ($key === "--replay" || $key === "-r") {
        showReplay($id);
    } elseif ($key === "--help" || $key === "-h") {
        help();
    } else {
        echo "Неверный ключ.";
    }
}

function cold_hot($myNumber, $numbers)
{
    $result = "Результаты ";
    for ($i = 0; $i < 3; $i++) {
        if ($myNumber[$i] == $numbers[$i]) {
            echo "Горячо!\n";
            $result .= " Горячо!;";
        } elseif (
            $myNumber[$i] == $numbers[0] ||
            $myNumber[$i] == $numbers[1] ||
            $myNumber[$i] == $numbers[2]
        ) {
            echo "Тепло!\n";
            $result .= " Тепло!;";
        } else {
            echo "Холодно!\n";
            $result .= " Холодно!;";
        }
    }
    return $result;
}

function restart()
{
    $restart = readline("Хотите сыграть ещё?[Y/N]\n");
    if ($restart === "Y") {
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
    // $numbers = str_split($numbers);
    $numbersInt = (int)join("", $numbers);
    $id = insertDB($numbersInt);

    $turn = 0;

    repeated($myNumber, $numbers, $id, $turn);
    restart();
}

function repeated($myNumber, $numbers, $id, $turn)
{
    $userNumber = readline("Введите трехзначное число : ");
    if (strlen($userNumber) == 3) {
        if (!is_array($userNumber)) {
            // $id = insertDB($numbers);
            $myNumber = str_split($userNumber);
        }
        if ($myNumber == $numbers) {
            $result = "Победа";
            updateDB($id, $result);
            echo "Вы выиграли!\n";
            $turn++;
            $turnRes = cold_hot($myNumber, $numbers);

            $turnResult = $turn . " | " . $userNumber . " | " . $turnRes;
            insertReplay($id, $turnResult);
        } else {
            //cold_hot($myNumber, $numbers);
            $turn++;
            $turnRes = cold_Hot($myNumber, $numbers);
            $turnResult = $turn . " | " . $userNumber . " | " . $turnRes;
            insertReplay($id, $turnResult);
            return repeated($myNumber, $numbers, $id, $turn);
        }
    } else {
        echo "Вы ввели либо НЕ число, либо не 3-х значное число!\n";
        return repeated($myNumber, $numbers, $id, $turn);
    }
}
