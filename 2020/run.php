<?php
ini_set('memory_limit', '800M');
require_once('./helper.php');

$data = loadData(2);

$coder = new Coder($data);

//$part1 = $coder->getProduct(2); // 605364
//$part2 = $coder->getProduct(3); // 128397680

$part1 = $coder->getTotalPassword(1); // 477
$part2 = $coder->getTotalPassword(2); // 686

dd($part1, $part2);
