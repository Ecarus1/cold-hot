<?php

namespace Ecarus1\coldhot\Controller;

use SQLite3;

use function Ecarus1\cold_hot\View\showGame;
// use function Ecarus1\cold_hot\View\showList;
// use function Ecarus1\cold_hot\View\showReplay;
use function Ecarus1\cold_hot\View\help;

    // function startGame() {
    //     echo "Game started".PHP_EOL;
    //     showGame();
    // }
function key($key, $id)
{
    if ($key == "--new" || $key == "-n") {
        startGame();
    } elseif ($key == "--list" || $key == "-l") {
        showList();
    } elseif ($key == "--replay" || $key == "-r") {
        showReplay($id);
    } elseif ($key == "--help" || $key == "-h") {
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
    $db = insertDB($numbers);

    $turn = 0;

    $id = $db->querySingle("SELECT gameId FROM games ORDER BY gameId DESC LIMIT 1");
    repeated($myNumber, $numbers, $id, $turn);
    restart();
}

function repeated($myNumber, $numbers, $id, $turn)
{
    $userNumber = readline("Введите трехзначное число : ");
    if (strlen($userNumber) == 3) {
        if (!is_array($userNumber)) {
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

function openDB()
{
    if (!file_exists("cold-hot.db")) {
        $db = createDB();
    } else {
        $db = new SQLite3("cold-hot.db");
    }
    return $db;
}

function createDB()
{
    $db = new SQLite3("cold-hot.db");
    $game = "CREATE TABLE games(
        gameId INTEGER PRIMARY KEY,
        gameDate DATE,
        gameTime TIME,
        playerName TEXT,
        secretNumber INTEGER,
        gameResult TEXT
    )";
    $db->exec($game);
    $turns = "CREATE TABLE info(
        gameId INTEGER,
        gameResult TEXT
    )";
    $db->exec($turns);
    return $db;
}

function insertDB($currentNumber)
{
    $db = openDB();

    date_default_timezone_set("Europe/Moscow");
    $gameData = date("d") . "." . date("m") . "." . date("Y");
    $gameTime = date("H") . ":" . date("i") . ":" . date("s");
    $playerName = getenv("username");
    $currentNumber = implode($currentNumber);

    $db->exec("INSERT INTO games (
        gameDate, 
        gameTime,
        playerName,
        secretNumber,
        gameResult
        ) VALUES (
        '$gameData', 
        '$gameTime',
        '$playerName',
        '$currentNumber',
        'Не закончено'
        )");

    return $db;
}

function updateDB($id, $result)
{
    $db = openDB();
    $db -> exec("UPDATE games
        SET gameResult = '$result'
        WHERE gameId = '$id'");
}

function insertReplay($id, $turnResult)
{
    $db = openDB();
    $db -> exec("INSERT INTO info (
    gameID,
    gameResult
    ) VALUES (
    '$id',
    '$turnResult')");
}

function showReplay($id)
{
    $db = openDB();
    $query = $db->query("SELECT Count(*) FROM info WHERE gameID = '$id'");
    $DBcheck = $query->fetchArray();
    if ($DBcheck[0] != 0) {
        \cli\line("Повтор игры с id = " . $id);
        $query = $db->query("SELECT gameResult FROM info WHERE gameID = '$id'");
        while ($row = $query->fetchArray()) {
            \cli\line("$row[0]");
        }
    } else {
        \cli\line("База данных пуста, либо не правильный id игры.");
    }
}

function showList()
{
    $db = openDB();
    $query = $db->query('SELECT Count(*) FROM games');
    $DBcheck = $query->fetchArray();
    $query = $db->query('SELECT * FROM games');
    if ($DBcheck[0] != 0) {
        while ($row = $query->fetchArray()) {
            \cli\line("ID $row[0])\n Дата: $row[1]\n Время: $row[2]
             Имя: $row[3]\n Загаданное число: $row[4]\n Результат: $row[5]");
        }
    } else {
        \cli\line("База данных пуста.");
    }
}
