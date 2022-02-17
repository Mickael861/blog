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
        $this->uri = $_SERVER['REQUEST_URI'];
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
    public function map(string $route, string $url, string $controller, string $view, string $folder = 'Utilisateurs')
    {
        $this->route[$route] = array(
            'url' => $url,
            'controller' => ucFirst($controller) . 'Controller',
            'view' => $view . 'Action',
            'folder' => $folder
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
            if (!empty($_GET)) {
                $this->route[$name[0]]['datas']['GET'] = $_GET;
            }
            
            if (!empty($_POST)) {
                $this->route[$name[0]]['datas']['POST'] = $_POST;
            }

            $paramsUrl = $this->getParamsUrl($name[0]);
            if (!empty($paramsUrl)) {
                $paramsUri = $this->getParamsUri($name[0]);
                if (sizeof($paramsUri) !== sizeof($paramsUrl)) {
                    throw new RouterException("The method for sending data does not exist");
                }

                $datas_url = array_combine($paramsUrl, $paramsUri);

                $this->route[$name[0]]['datas']['URL'] = $datas_url;
            }
             
            return $this->route[$name[0]];
        } else {
            throw new RouterException("No match route");
        }

        return false;
    }

    /**
     * Retrieve data from URI
     *
     * @param  string $name Name of the route
     * @return array a table of expected values
     */
    private function getParamsUri(string $name): array
    {
        $params = array();

        $paramsUri = explode('?', trim($this->uri, '/'));
        unset($paramsUri[1]);
        $paramsUri = explode('/', $paramsUri[0]);
        unset($paramsUri[0]);
        foreach ($paramsUri as $param) {
            if ($param !== '') {
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
        $uriExpected = explode(':', $this->route[$name]['url']);
        unset($uriExpected[0]);

        $url = array();
        foreach ($uriExpected as $param) {
            if (!empty($param)) {
                $param = str_replace('/', '', $param);
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
            $namespace = 'App\Controller\\' . $match['folder'] . '\\';
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
