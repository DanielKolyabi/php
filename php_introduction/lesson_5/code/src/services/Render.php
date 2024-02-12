<?php

namespace app\services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Render
{
    const CACHE_ENABLED = false;
    const CACHE_FOLDER = 'cache';

    static private Render $app;

    protected FilesystemLoader $loader;
    protected Environment $environment;

    static public function app(): Render
    {
        if (empty(self::$app)) {
            self::$app = new self();
        }
        return self::$app;
    }

    public function __construct()
    {
        $props = [];
        if (self::CACHE_ENABLED) {
            $props['cache'] = Helper::getPathRoot() . '/' . self::CACHE_FOLDER;
        }
        $this->loader = new FilesystemLoader(Helper::getView());
        $this->environment = new Environment($this->loader, $props);
//        try { // TODO remove
//        } catch (\Throwable $e) {
//            echo '<pre>';
//            print_r([
//                '$e2' => $e->getCode(),
//                '$e' => $e->getMessage(),
//            ]);
//            echo '</pre>';
//        }
    }

    public function renderPage(array $props = [], $tplContent = '', $tplMain = 'content/main'): string
    {
        $vars = array_merge($props, [
            'template_main' => $tplMain ? "$tplMain.twig" : null,
            'template_component' => $tplContent ? "$tplContent.twig" : null,
            'title' => $props['title'] ?? '',
            'description' => $props['description'] ?? '',
            'keywords' => $props['keywords'] ?? '',
            'canonical' => $props['canonical'] ?? '/' . ltrim($_SERVER['REQUEST_URI'], '/'),
            'content' => $props['content'] ?? '',
        ]);
        try {
            $template = $this->environment->load('main.twig');
            return $template->render($vars);
        } catch (\Throwable $e) {
            return "Error {$e->getCode()}: {$e->getMessage()}";
        }
    }

    public function renderError(string $message, int $code = 0): string
    {
        $errorName = "Ошибка " . ($code > 0 ? "$code" : "");
        $canonical = "/error" . ($code > 0 ? "$code" : "");
        return $this->renderPage([
            'title' => $errorName,
            'canonical' => $canonical,
            'error_name' => $errorName,
            'error_code' => $code,
            'error_message' => $message
        ], null, 'content/error');
    }
}