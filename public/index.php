<?php
use App\Core\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
session_start();

$router = new Router();

$router->map('home', '/home', 'home', 'home');
$router->map('posts', '/posts/:page', 'posts', 'posts');
$router->map('post', '/post/:slug/:id', 'post', 'post');
$router->map('login', '/login', 'login', 'login');
$router->map('signup', '/signup', 'signup', 'signup');
$router->map('admin', '/admin/home', 'home', 'home', 'admin');

$router->run();
