<?php

namespace Root\App\Controllers;

use Exception;
use Root\App\Models\GroupModel;
use Root\App\Models\UserModel;
use Root\App\Services\Render;

// TODO вынести в Core
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
            $users[] = [
                'id' => $user->id,
                'username' => $user->username,
                'group' => $user->group->name,
                'email' => $user->email,
                'birthday' => $user->birthday,
                'created_at' => $user->created_at,
            ];
        }
        
        $userData = [
            'user' => (\App::app()->user->data)(),
            'group' => (\App::app()->user->data->group)(),
        ];
        unset($userData['user']['auth_hash'], $userData['user']['password']);
        return Render::app()->renderPage([
            'title' => 'Список пользователей',
            'data' => $users,
            'userData' => \App::app()->user->isAuthorized ? $userData : null,
        ],  "$this->templateFolder/index");
    }
    
    /**
     * Get user
     * @throws Exception
     */
    public function actionGet(): string
    {
        try {
            $response = [];
            if (@$userId = $this->dataGet()['id']) {
                $user = UserModel::findByUnique($userId);
                $response['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'group_id' => $user->group_id,
                    'email' => $user->email,
                    'birthday' => $user->birthday,
                ];
            } else {
                foreach (GroupModel::getAll() as $key => $value) {
                    $response['groups'][$key] = [
                        'id' => $value->id,
                        'name' => $value->name,
                    ];
                }
            }
            return $this->response($response);
        } catch (\Throwable $e) {
            return $this->response(null, $e);
        }
    }
    
    /**
     * Create user
     * @throws Exception
     */
    public function actionCreate(): string
    {
        // TODO добавить проверку на группу (only user)
        try {
            $get = $this->dataGet();
            if (UserModel::find(['username' => $get['username']], true)) {
                throw new Exception('Username is taken');
            }
            $user = new UserModel();
            foreach ($get as $key => $value) {
                $user->$key = $value;
            }
            return $this->response($user->create());
        } catch (\Throwable $e) {
            return $this->response(null, $e);
        }
    }
    
    /**
     * Update user
     * @throws Exception
     */
    public function actionUpdate(): string
    {
        try {
            $currentUser = \App::app()->user;
            if (!$currentUser->isAuthorized || $currentUser->data->group->name !== 'admin') {
                throw new Exception('У вас нет прав на это действие', 403);
            }
            $get = $this->dataGet();
            $id = $get['id'];
            unset($get['id']);
            $user = UserModel::findByUnique($id);
            foreach ($get as $key => $value) {
                $user->$key = $value;
            }
            return $this->response($user && $user->update());
        } catch (\Throwable $e) {
            return $this->response(null, $e);
        }
    }
    
    /**
     * Delete user
     * @throws Exception
     */
    public function actionDelete(): string
    {
        try {
            $currentUser = \App::app()->user;
            if (!$currentUser->isAuthorized || $currentUser->data->group->name !== 'admin') {
                throw new Exception('У вас нет прав на это действие', 403);
            }
            $user = UserModel::findByUnique($this->dataGet()['id']);
            return $this->response($user && $user->delete());
        } catch (\Throwable $e) {
            return $this->response(null, $e);
        }
    }
}