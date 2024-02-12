<?php

namespace app\controllers;

use app\models\UserModel;
use app\services\Render;

class UsersController extends BaseController
{
    private const TEMPLATE_FOLDER = 'content/users/';

    public function actionIndex(): string
    {
        return Render::app()->renderPage([
            'title' => 'Список пользователей',
            'data' => UserModel::all(),
        ], self::TEMPLATE_FOLDER . 'index');
    }

    /**
     * @throws \Exception
     */
    public function actionProfile($username): string
    {
        $user = UserModel::findByUsername($username) ?? throw new \Exception('User not found!', 404);
        return Render::app()->renderPage([
            'title' => "Profile $user->username",
            'user' => (array)$user,
        ], self::TEMPLATE_FOLDER . 'profile');
    }

    /**
     * @throws \Exception
     */
    public function actionSave(): string
    {
        (new UserModel(self::dataGet()))->save();
        return Render::app()->renderPage([
            'title' => "Save user",
            'content' => 'User saved!'
        ]);
    }
}