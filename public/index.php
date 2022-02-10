<?php
use App\Core\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$router = new Router();

$router->map('home', 'GET|POST', '/home', 'home', 'home');
$router->map('posts', 'GET', '/posts/:page', 'posts', 'posts');
$router->map('post', 'GET|POST', '/post/:slug/:id', 'post', 'post');

$router->run();
