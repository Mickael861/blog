<?php
use App\Core\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
session_start();

$router = new Router();

$router->map('home', 'GET', '/home', 'home', 'home');
$router->map('posts', 'GET', '/posts/:page', 'posts', 'posts');
$router->map('post', 'GET|POST', '/post/:slug/:id', 'post', 'post');
$router->map('login', 'GET|POST', '/login', 'login', 'login');
$router->map('signup', 'GET|POST', '/signup', 'signup', 'signup');
$router->map('admin', 'GET|POST', '/admin/home', 'home', 'home', 'admin');

$router->run();
