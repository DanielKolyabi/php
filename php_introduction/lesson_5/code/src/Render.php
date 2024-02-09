<?php

namespace Geekbrains\Application1;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Render
{
    private string $viewFolder = '/src/Views/';
    private FilesystemLoader $loader;
    private Environment $environment;

    public function __construct()
    {
        $this->loader = new FilesystemLoader(dirname(__DIR__) . $this->viewFolder);
        $this->environment = new Environment($this->loader, [
            // 'cache' => $_SERVER['DOCUMENT_ROOT'].'/cache/',
        ]);
    }

    public function renderPage(string $tplName = 'page-view.twig', array $tplVars = []): string
    {
        $tplVars['content_template_name'] = $tplName;
        if (empty($tplVars['title'])) {
            $tplVars['title'] = 'Заголовок [title]';
        }
        if (empty($tplVars['content'])) {
            $tplVars['content'] = 'Блок контента [content]';
        }
        $tplVars['canonical'] = '/' . trim($_SERVER['REQUEST_URI'], '/');
        try {
            $template = $this->environment->load('main.twig');
            return $template->render($tplVars);
        } catch (\Throwable $e) {
            return "Error {$e->getCode()}: {$e->getMessage()}";
        }
    }

    public function renderError(int $code = 404, string $message = ''): string
    {
        $errorName = 'Ошибка';
        if ($code > 0) {
            $errorName .= " $code";
        }
        if ($code === 404 && empty($message)) {
            $message = 'Page not found!';
        }
        try {
            header("HTTP/1.1 404 Not Found");
            $template = $this->environment->load('main.twig');
            return $template->render([
                'content_template_name' => 'page-error.twig',
                'canonical' => '/error404',
                'title' => $errorName,
                'error_name' => $errorName,
                'error_code' => $code,
                'error_message' => $message,
            ]);
        } catch (\Throwable $e) {
            header("HTTP/1.1 500 Internal Server Error");
            return "Error {$e->getCode()}: {$e->getMessage()}";
        }
    }
}
