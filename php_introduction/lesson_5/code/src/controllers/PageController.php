<?php

namespace app\controllers;

use app\services\Helper;
use app\services\Render;

class PageController
{
    const TEMPLATE_FOLDER = 'content/page';

    protected ?string $template = null;

    protected function getDefaultVariables(): array
    {
        return [];
    }

    /**
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        if (str_starts_with($name, 'action')) {
            $actionName = substr($name, 6);
            $canonical = lcfirst($actionName);
            $templateContent = implode('/', [self::TEMPLATE_FOLDER, $canonical]);
            if (!file_exists(Helper::getView("$templateContent.twig"))) {
                throw new \Exception('Page not found!', 404);
            }
            $modelName = Helper::getModel($actionName . 'Model');
            $model = class_exists($modelName) ? (array)new $modelName() : [];
            $vars = [
                'title' => $actionName,
                'canonical' => $canonical !== 'index' ? "/$canonical" : "/",
                ...$this->getDefaultVariables(),
                ...$model,
            ];
            return Render::app()->renderPage($vars, $templateContent);
        }
        throw new \Exception('Page not found!', 404);
    }

//    public function actionIndex(): string
//    {
//        return Render::app()->renderPage([
//            'title' => 'Главная страница',
//            'content' => 'Блок контента главной страницы',
//        ]);
//    }
}