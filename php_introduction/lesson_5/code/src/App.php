<?php

namespace app;

use app\services\Helper;
use app\services\Render;

class App
{
    static public function run(): string
    {
        $url = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
        if (count($url) < 2 || empty($controllerName = array_shift($url))) {
            $controllerName = 'page';
        }
        if (empty($methodName = array_shift($url))) {
            $methodName = 'index';
        }
        $controllerName = Helper::getController(ucfirst($controllerName) . 'Controller');
        $methodName = 'action' . ucfirst($methodName);


//        echo '<pre>';
//        print_r([
//            '$controllerName' => $controllerName,
//            '$methodName' => $methodName,
//            '$url' => $url,
//        ]);
//        echo '</pre>';


        try {
            if (!class_exists($controllerName)) {
                throw new \Exception('Page not found!', 404);
            }
            if (!method_exists($controllerName, $methodName) && !method_exists($controllerName, '__call')) {
                throw new \Exception('Page not found!', 404);
            }
            return (new $controllerName())->$methodName($url);
        } catch (\Throwable $e) {
            header("HTTP/1.1 {$e->getCode()}");
            return Render::app()->renderError($e->getMessage(), $e->getCode());
        }
    }
}