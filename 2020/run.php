<?php
ini_set('memory_limit', '3072M');
require_once('./helper.php');

$day = 20;
$coder = new Coder($day);
[$part1, $part2] = $coder->run();

dd($part1, $part2);
