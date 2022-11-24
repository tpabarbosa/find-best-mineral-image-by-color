<?php

require_once __DIR__ . '../vendor/autoload.php';
require_once __DIR__ . '../src/bootstrap.php';

use Tpabarbosa\FindBestImageByColor\ColorMatcher;

$userColorHex = null;
$quantity = 5;
$result = [];

if (isset($_POST['submit'])) {
    $userColorHex = $_POST['color'];
    $quantity = $_POST['quantity'];
    $picker = new ColorMatcher($data, $userColorHex);
    $result = $picker->search($quantity);
}

$toView = [
    'color' => $userColorHex,
    'result' => $result,
    'quantity' => $quantity,
];

echo $twig->render('index.twig', $toView);
