<?php

$vendorGit = __DIR__ . '/../vendor/autoload.php';
$autoPackagist = __DIR__ . '/../../../autoload.php';

if (file_exists($vendorGit)) {
    require_once($vendorGit);
} else {
    require_once($autoPackagist);
}

use function Ecarus1\coldhot\Controller\key;

if (isset($argv[1])) {
    $key = $argv[1];
    key($key, $argv[2]);
} else {
    $key = "-n";
    $argv[2] = 0;
    key($key, $argv[2]);
}
