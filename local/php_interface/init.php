<?php
define('VUEJS_DEBUG', true);

if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    include_once __DIR__ . "/../vendor/autoload.php";
}

if (file_exists(__DIR__ . "/events.php")) {
    include_once __DIR__ . "/events.php";
}