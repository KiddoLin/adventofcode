<?php
ini_set('memory_limit', '800M');
require_once('./helper.php');

$data = loadData(1);

$coder = new Coder($data);

$part1 = $coder->getProduct(2); // 605364
$part2 = $coder->getProduct(3); // 128397680

dd($part1, $part2);
