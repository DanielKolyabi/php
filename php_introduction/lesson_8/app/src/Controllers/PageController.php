<?php

namespace Root\App\Controllers;

class PageController extends BaseController
{
    protected ?string $modelName = 'page';
    protected ?string $templateFolder = 'content/page';
    
    // public function actionIndex(): string
    // {
    //     return Render::app()->renderPage([
    //         'title' => 'Главная страница',
    //         'content' => 'Блок контента главной страницы',
    //     ]);
    // }
}