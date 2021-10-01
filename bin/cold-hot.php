<?php

$vendorGit = __DIR__ . '/../vendor/autoload.php';
$autoPackagist = __DIR__ . '/../../../autoload.php';

if (file_exists($vendorGit)) {
    require_once($vendorGit);
} else {
    require_once($autoPackagist);
}

use function Ecarus1\coldhot\Controller\key;

key();
