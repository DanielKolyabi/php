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
        
        // TODO добавить авторизацию
        
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
     * @param UserModel $user
     * @return string
     */
    protected function getAuthHash(UserModel $user): string
    {
        $data = [
            'username' => $user->username,
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'datetime' => date('Y-m-d H:i', time()),
        ];
        
        $salt = md5("{$user->username}{$user->password}");
        $data['hash'] = md5(implode([...array_values($data), $salt]));
        
        return base64_encode(json_encode($data));
    }
    
    /**
     * @return UserModel|false
     */
    protected function checkAuthHash(): UserModel|false
    {
        // TODO вынести вызов в App
        try {
            if (!($hash = @$_COOKIE['authHash'])) {
                throw new Exception('AuthCookie not found');
            }
            if (!($user = UserModel::find(['auth_hash' => $hash], true))) {
                throw new Exception('User not found');
            }
            $cookie = json_decode(base64_decode($hash), true);
            if ($user->username !== $cookie['username']) {
                throw new Exception('Wrong user found');
            }
            if ($_SERVER['HTTP_USER_AGENT'] !== $cookie['useragent']) {
                throw new Exception('User changed browser');
            }
            $salt = md5("{$user->username}{$user->password}");
            $md5Cookie = $cookie['hash'];
            unset($cookie['hash']);
            $md5Check = md5(implode([...array_values($cookie), $salt]));
            if ($md5Cookie !== $md5Check) {
                throw new Exception('Hashes don\'t match');
            }
            return $user;
        } catch (\Throwable) {
            setcookie('authHash', '');
            session_destroy();
            return false;
        }
    }
    
    // TODO добавить обработку рус. языка для josn_encode
    public function actionAuth(): bool|string
    {
        try {
            $data = $this->dataGet();
            
            $user = UserModel::find([
                'username' => $data['username'],
                'password' => md5($data['password']),
            ], true);
            
            if (!$user) {
                throw new Exception('Логин или пароль указаны неверно');
            }
            
            $user->auth_hash = $this->getAuthHash($user);
            if ($user->save()) {
                setcookie('authHash', $user->auth_hash, time() + 60 * 60 * 24 * 7);
            }
            
            // echo '<pre>';
            // print_r([
            //     'hash' => $user->auth_hash,
            //     'check' => $this->checkAuthHash(),
            //     // 'user' => $user(),
            //     // '_SESSION' => $_SESSION,
            //     // '_COOKIE' => $_COOKIE,
            //     // '_SERVER' => $_SERVER['HTTP_USER_AGENT'],
            //
            //     // '_SERVER' => $_SERVER['HTTP_X_FORWARDED_FOR'],
            //     // '_SERVER' => $_SERVER,
            //     // '_COOKIE' => session_get_cookie_params(),
            //     // 'key' => $key,
            //     // 'hashData' => $hashData,
            // ]);
            // echo '</pre>';
            
            return json_encode(['data' => true]);
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
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
            return json_encode(['data' => $response]);
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