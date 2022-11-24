<?php

use Tpabarbosa\FindBestImageByColor\ImagesColorData;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/bootstrap.php';

$toView = [
    'minerals' => $data,
    'size' => ImagesColorData::EXTRACT,
];

echo $twig->render('minerals.twig', $toView);
