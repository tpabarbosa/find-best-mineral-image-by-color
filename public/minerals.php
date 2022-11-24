<?php

use Tpabarbosa\FindBestImageByColor\ImagesColorData;

require_once '../vendor/autoload.php';
require_once '../src/bootstrap.php';

$toView = [
    'minerals' => $data,
    'size' => ImagesColorData::EXTRACT,
];

echo $twig->render('minerals.twig', $toView);
