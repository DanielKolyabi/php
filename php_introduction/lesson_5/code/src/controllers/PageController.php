<?php

namespace app\controllers;

class PageController extends BaseController
{
    protected static ?string $modelName = 'page';
    protected static ?string $templateFolder = 'content/page';

//    public function actionIndex(): string
//    {
//        return Render::app()->renderPage([
//            'title' => 'Главная страница',
//            'content' => 'Блок контента главной страницы',
//        ]);
//    }
}