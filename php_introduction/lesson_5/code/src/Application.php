<?php

namespace Geekbrains\Application1;

class Application
{
    private const APP_NAMESPACE = ['Geekbrains\Application1', 'Controllers'];
    private string $controllerName;
    private string $methodName;

    public function run(): string
    {
        $url = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
        if (empty($methodName = array_pop($url))) {
            $methodName = 'index';
        }
        $methodName = 'action' . ucfirst($methodName);
        if (!count($url) || empty($controllerName = array_pop($url))) {
            $controllerName = 'page';
        }
        $controllerName = ucfirst($controllerName) . 'Controller';
        try {
            $this->controllerName = implode('\\', [...self::APP_NAMESPACE, ...$url, $controllerName]);
            $this->methodName = $methodName;
            return call_user_func([(new $this->controllerName()), $this->methodName]);
        } catch (\Throwable $e) {
            return (new Render())->renderError($e->getCode(), $e->getMessage());
        }
    }
}