<?php

require_once('./Coder.php');

function dd(...$args)
{
    foreach ($args as $arg) {
        dump($arg);
    }
    exit();
}

function dump(...$args)
{
    foreach ($args as $arg) {
        print_r($arg);
    }
    echo PHP_EOL;
}

function loadData(int $no)
{
    $str = require_once("./{$no}.php");
    $data = explode(PHP_EOL, $str);
    return $data;
}
