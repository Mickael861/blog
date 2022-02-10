<?php

namespace App\Core;

use App\Exception\RouterException;

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
            'method' => $method,
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
        $method_exist = false;

        if ($this->uri === '/') {
            $name[] = 'home';
        } else {
            $name = substr($this->uri, 1);
            $name = explode('/', $name);
        }
        
        if (key_exists($name[0], $this->route)) {
            if ($this->route[$name[0]]['method'] === 'GET') {
                $method_exist = true;

                if (!empty($_GET)) {
                    $this->route[$name[0]]['datas']['GET'] = $_GET;
                }

                $this->makeDatasUrl($name[0]);
            }
            
  
            if ($this->route[$name[0]]['method'] === 'POST') {
                $method_exist = true;

                if (!empty($_GET)) {
                    $this->route[$name[0]]['datas']['GET'] = $_GET;
                }
                
                if (!empty($_POST)) {
                    $this->route[$name[0]]['datas']['POST'] = $_POST;
                } else {
                    throw new RouterException("POST data is expected");
                }

                $this->makeDatasUrl($name[0]);
            }

            if ($this->route[$name[0]]['method'] === 'GET|POST') {
                $method_exist = true;

                if (!empty($_GET)) {
                    $this->route[$name[0]]['datas']['GET'] = $_GET;
                }
                
                if (!empty($_POST)) {
                    $this->route[$name[0]]['datas']['POST'] = $_POST;
                }

                $this->makeDatasUrl($name[0]); 
            }

            if (!$method_exist) {
                throw new RouterException("The method for sending data does not exist");
            }
            
            return $this->route[$name[0]];
        } else {
            throw new RouterException("No match route");
        }

        return false;
    }

    /**
     * Retrieve data from URL
     *
     * @param  string $name Le nom
     * @return void
     */
    private function makeDatasUrl(string $name): void
    {
        $url = $this->getParamsUrl($name);

        if (!empty($url)) {
            $params = $this->getParamsUri($name);

            if (sizeof($url) < sizeof($params)) {
                throw new RouterException("URL has too much data");
            } elseif (sizeof($url) !== sizeof($params)) {
                throw new RouterException("URL has insufficient data");
            } else {
                $datas_url = array_combine($url, $params);
                foreach ($datas_url as $key => $data) {
                    $this->route[$name]['datas']['GET'][$key] = $data;
                }
            }
        } else {
            $params = $this->getParamsUri($name);
            
            if (!empty($params)) {
                throw new RouterException("The URL does not match the expected URL");
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
        try {
            $match = $this->match();

            $namespace = 'App\Controller\\';
            $controller = $namespace . $match['controller'];
            $controller = new $controller;

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
