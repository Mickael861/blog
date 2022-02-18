<?php

namespace App\Core;

use App\Exception\RouterException;

class Router
{

    /**
     * @var array
     */
    private $route = array();

    public function __construct()
    {
        $this->uri = trim($_SERVER['REQUEST_URI'], '/');
    }
        
    /**
     * Records routes
     *
     * @param  string $route name of the route
     * @param  string $url URL excpected
     * @param  string $controller name of the controller
     * @param  string $view name of view
     * @param  string $folder name of folder
     * @return void
     */
    public function map(
        string $route,
        string $url,
        string $controller,
        string $view,
        string $folder = 'Utilisateurs',
        array $params = array()
    ) {
        $this->route[$route] = array(
            'url' => $url,
            'controller' => ucFirst($controller) . 'Controller',
            'view' => $view . 'Action',
            'folder' => $folder,
            'params' => $params
        );
    }
        
    /**
     * If an URL match with routes
     *
     * @return array|false No match returns false, otherwise the route
     */
    private function match()
    {
        $uri = explode('?', $this->uri);
        $uri = trim($uri[0], '/');
        if ($uri === "") {
            $uri = '/';
        }
        
        foreach ($this->route as $route_name => $route) {
            $path = preg_replace('#:([\w]+)#', '([^/]+)', $route['url']);
            $regex = "#^$path$#i";

            if (preg_match($regex, $uri, $matches)) {
                $params = $matches;
                $route_name = $route_name;
                break;
            }
        }
        
        if (empty($params)) {
            throw new RouterException('No match route');
        }
        
        $this->route[$route_name]['datas']['GET'] = !empty($_GET) ? $_GET : array();
        $this->route[$route_name]['datas']['POST'] = !empty($_POST) ? $_POST : array();
  
        if (!empty($params) && !empty($route_name)) {
            array_shift($params);
            
            $this->route[$route_name]['datas']['match'] = array_combine($this->route[$route_name]['params'], $params);
        }
        
        return $this->route[$route_name];
    }

    /**
     * Call Controller
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $match = $this->match();
            
            $namespace = '\App\Controller\\' . $match['folder'] . '\\' . $match['controller'];
            $controller = new $namespace;

            if (!empty($match['datas'])) {
                $controller->{$match['view']}($match['datas']);
            } else {
                $controller->{$match['view']}();
            }
        } catch (RouterException $e) {
            require_once dirname(__DIR__, 2) . '/views/error404.twig';
        }
    }
}
