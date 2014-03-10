<?php

require '../vendor/autoload.php';

date_default_timezone_set('Europe/Copenhagen');

$app = new \Slim\Slim();

$app->config(array(
    'templates.path' => '../templates'
));

$app->view(new \Slim\Views\Twig());

$postLoader = new \Thin\PostLoader;
$postLoader->config(array(
    'document_path' => '../content',
    'document_ext' => '.md'
));

$app->get('/', function () use ($app, $postLoader)
{
    $posts = $postLoader->all();

    $app->render('index.html', array('posts' => $posts));
});

$app->get('/blog', function () use ($app, $postLoader)
{
    $posts = $postLoader->all();

    $app->render('blog.html', array('posts' => $posts));
});

$app->get('/blog/:slug', function ($slug) use ($app, $postLoader)
{
    $post = $postLoader->find($slug);
    $parser = new \Thin\Parsers\MarkdownExtraParser;

    $app->render('post.html', array('post' => $post, 'parser' => $parser));
});

$app->run();