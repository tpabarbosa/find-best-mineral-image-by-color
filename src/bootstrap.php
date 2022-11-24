<?php

use Tpabarbosa\FindBestImageByColor\ImagesColorData;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../src/templates');
$twig = new Environment($loader, [
    'cache' => false,
]);

$images = new ImagesColorData(__DIR__ . '/../public/images', __DIR__ . '/data.json');
$data = $images->getData();
