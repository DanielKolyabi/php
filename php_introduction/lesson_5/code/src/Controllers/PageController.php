<?php

namespace Geekbrains\Application1\Controllers;

use Geekbrains\Application1\Render;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PageController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionIndex(): string
    {
        return (new Render())->renderPage('page-content.twig', [
            'title' => 'Главная страница',
            'content' => 'Блок контента главной страницы',
        ]);
    }

    public function actionError404(): string
    {
        return (new Render())->renderError(404, 'Page not found!');
    }
}