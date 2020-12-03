<?php
ini_set('memory_limit', '800M');
require_once('./helper.php');

$data = loadData(3);

$coder = new Coder($data);

//$part1 = $coder->getProduct(2); // 605364
//$part2 = $coder->getProduct(3); // 128397680

//$part1 = $coder->getTotalPassword(1); // 477
//$part2 = $coder->getTotalPassword(2); // 686

$part1 = $coder->getTotalTrees(); // 254
$part2 = $coder->getTotalTreeProduct(); // 1666768320

dd($part1, $part2);
