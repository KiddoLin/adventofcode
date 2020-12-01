<?php

require_once('./Coder.php');

function dd($data)
{
    dump($data);
    exit();
}

function dump($data)
{
    print_r($data);
    echo PHP_EOL;
}


