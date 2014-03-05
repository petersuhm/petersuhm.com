<?php

require '../vendor/autoload.php';

$app = new \Slim\Slim();

$app->config(array(
	'templates.path' => '../templates'
));

$app->view(new \Slim\Views\Twig());

$app->get('/', function () use ($app) {
    $app->render('index.html');
});

$app->run();