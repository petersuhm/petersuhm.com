<?php

require '../vendor/autoload.php';

date_default_timezone_set('Europe/Copenhagen');

$app = new \Slim\Slim();

$app->config(array(
    'templates.path' => '../templates'
));

$app->view(new \Slim\Views\Twig());

$flat = new \Petersuhm\Flat\Flat();

$app->get('/', function () use ($app, $flat)
{
    $posts = $flat->posts();

    $app->render('index.html', array('posts' => $posts));
});

$app->get('/blog', function () use ($app, $flat)
{
    $posts = $flat->posts();

    $app->render('blog.html', array('posts' => $posts));
});

$app->get('/blog/:slug', function ($slug) use ($app, $flat)
{
    $post = $flat->post($slug);

    $app->render('post.html', array('post' => $post));
});

$app->run();