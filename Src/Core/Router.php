<?php

namespace App\Core;

use App\Controller\Controller;
use Exception;

/**
 * Router
 */
class Router
{

    /**
     * The list of existing controllers
     *
     * @var array
     */
    private $route = array();

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }
        
    /**
     * Records routes
     *
     * @param  string $route name of the route
     * @param  string $method method of send datas
     * @param  string $url URL excpected
     * @param  string $controller name of the controller
     * @param  string $view name of view
     * @return void
     */
    public function map(string $route, string $method, string $url, string $controller, string $view): void
    {
        $this->route[$route] = array(
            'key' => $method,
            'url' => $url,
            'controller' => ucFirst($controller) . 'Controller',
            'view' => $view . 'Action'
        );
    }
        
    /**
     * If an URL match with routes
     *
     * @return array|false No match returns false, otherwise the route
     */
    private function match()
    {
        if ($this->uri === '/') {
            $name[] = 'home';
        } else {
            $name = substr($this->uri, 1);
            $name = explode('/', $name);
        }
        
        if (key_exists($name[0], $this->route)) {
            if ($this->route[$name[0]]['key'] === 'GET') {
                if (!empty($_GET)) {
                    $this->route[$name[0]]['datas']['GET'] = $_GET;
                }

                $this->makeDatasUrl($name[0]);
            }
            
  
            if ($this->route[$name[0]]['key'] === 'POST') {
                if (!empty($_GET)) {
                    $this->route[$name[0]]['datas']['GET'] = $_GET;
                }
                
                if (!empty($_POST)) {
                    $this->route[$name[0]]['datas']['POST'] = $_POST;
                } else {
                    throw new Exception("Une donnée en POST est attendue");
                }

                $this->makeDatasUrl($name[0]);
            }

            return $this->route[$name[0]];
        }

        return false;
    }

    /**
     * Retrieve data from URL
     *
     * @param  string $name Le nom
     * @return void
     */
    private function makeDatasUrl(string $name)
    {
        $url = $this->getParamsUrl($name);

        if (!empty($url)) {
            $params = $this->getParamsUri($name);

            if (sizeof($url) < sizeof($params)) {
                throw new Exception("L\'URL comporte trop de données");
            } elseif (sizeof($url) !== sizeof($params)) {
                throw new Exception("L\'URL comporte pas assez de données");
            } else {
                $datas_url = array_combine($url, $params);
                foreach ($datas_url as $key => $data) {
                    $this->route[$name]['datas']['GET'][$key] = $data;
                }
            }
        } else {
            $params = $this->getParamsUri($name);
            
            if (!empty($params)) {
                throw new Exception("L\'URL ne correspond pas a l\'URL attendue");
            }
        }
    }

    /**
     * Retrieve data from URI
     *
     * @param  string $name Name of the route
     * @return array a table of expected values
     */
    private function getParamsUri(string $name): array
    {
        $uri = explode('?', trim($this->uri, '/'));
        $uri = explode('/', $uri[0]);

        $params = array();
        foreach ($uri as $param) {
            if ($param !== $name && $param !== "") {
                $params[] = $param;
            }
        }

        return $params;
    }
    
    /**
     * Retrieve data from the URL passed to the route
     *
     * @param  string $name Le nom
     * @return array a table of expected values
     */
    private function getParamsUrl(string $name): array
    {
        $uriExpected = substr($this->route[$name]['url'], 1);
        $uriExpected = str_replace('/', '', $uriExpected);
        $uriExpected = explode(':', $uriExpected);

        $url = array();
        foreach ($uriExpected as $param) {
            if ($param !== $name && $param !== "") {
                $url[] = $param;
            }
        }

        return $url;
    }

    /**
     * Call Controller
     *
     * @return void
     */
    public function run(): void
    {
        $match = $this->match();

        if (!empty($match)) {
            $namespace = 'App\Controller\\';
            $controller = $namespace . $match['controller'];
            $controller = new $controller;

            if (isset($match['datas'])) {
                $controller->{$match['view']}($match['datas']);
            } else {
                $controller->{$match['view']}();
            }
        } else {
            require_once dirname(__DIR__, 2) . '/views/error404.twig';
        }
    }
}