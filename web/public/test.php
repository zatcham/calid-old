<?php
require '..\vendor\autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

echo $twig->render('first.html.twig', ['name' => 'John Doe',
    'occupation' => 'gardener']);