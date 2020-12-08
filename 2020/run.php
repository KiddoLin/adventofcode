<?php
ini_set('memory_limit', '800M');
require_once('./helper.php');

$data = loadData(8);

$coder = new Coder($data);

//$part1 = $coder->getProduct(2); // 605364
//$part2 = $coder->getProduct(3); // 128397680

//$part1 = $coder->getTotalPassword(1); // 477
//$part2 = $coder->getTotalPassword(2); // 686

//$part1 = $coder->getTotalTrees(); // 254
//$part2 = $coder->getTotalTreeProduct(); // 1666768320

//$part1 = $coder->getDay4(1); // 254
//$part2 = $coder->getDay4(); // 133

//$part1 = $coder->getMaxBoardingPassId(); // 922
//$part2 = $coder->getMyBoardingPassId(); // 747

// $part1 = $coder->getBagTotalParents(); // 268
// $part2 = $coder->getBagTotalChildren(); // 7867

$part1 = $coder->getDay8(1); // 1675
$part2 = $coder->getDay8(2); // 1532

dd($part1, $part2);
