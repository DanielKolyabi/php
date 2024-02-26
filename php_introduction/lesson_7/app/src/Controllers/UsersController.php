<?php

namespace Root\App\Controllers;

use Exception;
use Root\App\Models\UserModel;
use Root\App\Services\Render;

class UsersController extends BaseController
{
    protected ?string $templateFolder = 'content/users';
    // protected string $model = UserModel::class;
    
    /**
     * @throws Exception
     */
    public function actionIndex(): string
    {
        $get = (object)$this->dataGet();
        
        $page = empty($get->page) || $get->page < 1 ? 1 : $get->page;
        
        $users = [];
        foreach (UserModel::getAll($page - 1) as $user) {
            $users[] = $user();
        }
        
        // echo '<pre>';
        // print_r($users);
        // echo '</pre>';
        // die;
        
        return Render::app()->renderPage([
            'title' => 'Список пользователей',
            'data' => $users,
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
            $user = UserModel::findByUnique($this->dataGet()['id']);
            return json_encode(['data' => $user()]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Create user
     * @throws Exception
     */
    public function actionCreate(): string
    {
        try {
            $get = $this->dataGet();
            $user = new UserModel($get);
            return json_encode(['data' => $user->create()]);
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
            $get = $this->dataGet();
            $id = $get['id'];
            unset($get['id']);
            $user = UserModel::findByUnique($id);
            
            echo '<pre>';
            print_r($get);
            echo '</pre>';
            
            if (($oldPass = $get['passwordOld']) || $get['passwordNew']) {
                if (md5($oldPass) === $user->password) {
                    $get['password'] = $get['passwordNew'];
                }
                unset($get['passwordOld'], $get['passwordNew']);
            }
            
            foreach ($get as $key => $value) {
                $user->$key = $value;
            }
            return json_encode(['data' => $user && $user->update()]);
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
            $user = UserModel::findByUnique($this->dataGet()['id']);
            return json_encode(['data' => $user && $user->delete()]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}