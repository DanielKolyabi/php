<?php

namespace app\controllers;

use app\models\UserModel;
use app\services\Helper;
use app\services\Render;

abstract class BaseController
{
    static protected ?string $templateFolder = null; // ex: content/page
    static protected ?string $modelName = null; // ex: AboutModel
    static protected array $modelProps = []; // ex: AboutModel

    /**
     * @throws \Exception
     */
    protected function getTemplate(string $action = null): ?string
    {
        $response = null;
        if (static::$templateFolder) {
            $template = static::$templateFolder;
            $action = lcfirst($action);
            if (file_exists(Helper::getView("$template/$action.twig"))) {
                $response = "$template/$action";
            } else if (file_exists(Helper::getView("$template.twig"))) {
                $response = $template;
            } else {
                throw new \Exception('Page not found!', 404);
            }
        }
        return $response;
    }

    protected function getModelData(string $action = null): array
    {
        if (static::$modelName) {
            $modelName = ucfirst(static::$modelName);
            $action = ucfirst($action);
            if (class_exists($mn = Helper::getModel("{$modelName}{$action}Model"))) {
                return (array)new $mn(static::$modelProps);
            } else if (class_exists($mn = Helper::getModel("{$modelName}Model"))) {
                return (array)new $mn(static::$modelProps);
            }
        }
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
            $vars = array_merge(
                [
                    'title' => $actionName,
                    'canonical' => $canonical !== 'index' ? "/$canonical" : "/",
                ],
                // $this->getDefaultVariables(), // TODO add
                $this->getModelData($actionName),
            );
            return Render::app()->renderPage($vars, $this->getTemplate($actionName));
        }
        throw new \Exception('Page not found!', 404);
    }

    protected function dataGet(): array
    {
        return $this->encodeData($_GET);
    }

    protected function dataPost(): array
    {
        return $this->encodeData($_POST);
    }

    protected function encodeData($data): array
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
        );
        $json = str_replace('\\', '\\\\\\', $json);
        return json_decode($json, true);
    }
}