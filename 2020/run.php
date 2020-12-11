<?php
ini_set('memory_limit', '2048M');
require_once('./helper.php');

$day = 10;
$coder = new Coder($day);
[$part1, $part2] = $coder->run();

dd($part1, $part2);
