<?php

namespace Root\App\Controllers;

use Exception;
use Root\App\Services\Helper;
use Root\App\Services\Render;

// TODO переработать Render -> __call (err404)
abstract class BaseController
{
    protected ?string $templateFolder = null; // ex: content/page
    protected ?string $modelName = null; // ex: AboutModel
    protected array $modelProps = []; // ex: AboutModel
    
    /**
     * @throws Exception
     */
    protected function getTemplate(string $action = null): ?string
    {
        $response = null;
        if ($this->templateFolder) {
            $template = $this->templateFolder;
            $action = lcfirst($action);
            if (file_exists(Helper::getViewPath($template, "$action.twig"))) {
                $response = "$template/$action";
            } elseif (file_exists(Helper::getViewPath("$template.twig"))) {
                $response = $template;
            } else {
                throw new Exception('Page not found!', 404);
            }
        }
        return $response;
    }
    
    protected function getModelData(string $action = null): array
    {
        if ($this->modelName) {
            $modelName = ucfirst($this->modelName);
            $action = ucfirst($action);
            if (class_exists($mn = Helper::getModel("{$modelName}{$action}Model"))) {
                return (array)new $mn($this->modelProps);
            } elseif (class_exists($mn = Helper::getModel("{$modelName}Model"))) {
                return (array)new $mn($this->modelProps);
            }
        }
        return [];
    }
    
    /**
     * @throws Exception
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
            return Render::app()->renderPage($vars, $this->getTemplate($actionName)); // TODO handler 404
        }
        throw new Exception('Page not found!', 404);
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
        $data = preg_replace('#<(script|link|meta|style|i?frame).*>#is', '$2', $data);
        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
        );
        $json = str_replace('\\', '\\\\\\', $json);
        return json_decode($json, true);
    }
    
    /**
     * @param mixed $data
     * @param ?mixed $error
     * @return bool|string
     */
    protected function response(mixed $data, mixed $error = null): bool|string
    {
        if ($error instanceof \Throwable) {
            $error = [
                'code' => $error->getCode(),
                'message' => $error->getMessage(),
            ];
        }
        return json_encode(['data' => $data, 'error' => $error], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}