<?php

namespace Root\App\controllers;

use Exception;
use Root\App\models\UserModel;
use Root\App\services\Render;

class UsersController extends BaseController
{
    protected ?string $templateFolder = 'content/users';
    
    public function actionIndex(): string
    {
        return Render::app()->renderPage([
            'title' => 'Список пользователей',
            'data' => UserModel::all(),
        ],  "$this->templateFolder/index");
    }
    
    /**
     * @throws Exception
     */
    /*
    // TODO вернуть на место
    public function actionProfile($username): string
    {
        $user = UserModel::findByUsername($username) ?? throw new Exception('User not found!', 404);
        return Render::app()->renderPage([
            'title' => "Profile $user->username",
            'user' => (array)$user,
        ], "$this->templateFolder/profile");
    }
    */
    
    /**
     * Get user
     * @throws Exception
     */
    public function actionGet(): string
    {
        try {
            $data = $this->dataGet();
            if (!($user = UserModel::findById($data['id']))) {
                throw new Exception('User not exist!');
            }
            return json_encode(['data' => $user]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Create user
     * @throws Exception
     */
    public function actionSave(): string
    {
        try {
            (new UserModel($this->dataGet()))->save();
            return json_encode(['data' => true]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Update user
     * @throws Exception
     */
    public function actionUpdate(): string
    {
        try {
            $data = $this->dataGet();
            $userID = $data['id'] ?? null;
            unset($data['id']);
            
            if (!($user = UserModel::findById($userID))) {
                throw new Exception('User not exist!');
            }
            foreach ($data as $key => $value) {
                $user->$key = $value;
            }
            $user->save();
            return json_encode(['data' => true]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Delete user
     * @throws Exception
     */
    public function actionDelete(): string
    {
        try {
            if (!($user = UserModel::findById($this->dataGet()['id']))) {
                throw new Exception('User not exist!');
            }
            $user->remove();
            return json_encode(['data' => true]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}