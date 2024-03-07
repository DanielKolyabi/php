<?php

namespace Root\App\Controllers;

use Exception;
use Root\App\Models\UserModel;

// TODO вынести в Core
class UserController extends BaseController
{
    const HASH_NAME = 'authHash';
    static private bool $isInit = false;
    public bool $isAuthorized = false;
    public UserModel $data;
    
    
    public function __construct()
    {
        try {
            if (!self::$isInit) {
                session_start();
                self::$isInit = true;
            }
            $session = &$_SESSION[static::HASH_NAME];
            $cookie = &$_COOKIE[static::HASH_NAME];
            $authHash = $session ?? $cookie;
            if (empty($session) && !empty($cookie)) {
                $session = $cookie;
            }
            if (empty($authHash)) {
                throw new Exception('authHash is empty');
            }
            if (!($user = UserModel::find(['auth_hash' => $authHash], true))) {
                throw new Exception('user not found');
            }
            if (!$this->authHashCheck($user, $authHash)) {
                throw new Exception('incorrect hash');
            }
            $this->data = $user;
            $this->isAuthorized = true;
        } catch (\Throwable) {
            $this->data = new UserModel();
            $this->authHashChange();
        }
    }
    
    public function __destruct()
    {
        if (self::$isInit && empty($_SESSION[static::HASH_NAME])) {
            session_destroy();
            self::$isInit = false;
        }
    }
    
    
    public function actionAuth(): bool|string
    {
        $data = $this->dataPost(); // $this->dataGet();
        try {
            if ($this->isAuthorized) {
                throw new Exception('Вы уже авторизованы');
            }
            if (empty($data['username'])) {
                throw new Exception('Не указан логин', 400);
            }
            if (empty($data['password'])) {
                throw new Exception('Не указан пароль', 400);
            }
            $user = UserModel::find(['username' => $data['username']], true);
            if (!$user || !password_verify($data['password'], $user->password)) {
                throw new Exception('Неверный логин или пароль', 401);
            }
            $hash = $user->auth_hash = $this->authHashGet($user);
            if (!$hash || !$user->save(false)) {
                throw new Exception('Ошибка авторизации, обратитесь к системному администратору', 403);
            }
            @$this->authHashChange($hash, $data['remember'] === 'true');
            return $this->response(true);
        } catch (\Throwable $e) {
            if ($e->getCode() > 0) {
                header("HTTP/1.1 {$e->getCode()}");
            }
            return $this->response(null, $e);
        }
    }
    
    public function actionLogout(): bool|string
    {
        try {
            $this->authHashChange();
            if (!$this->isAuthorized) {
                throw new Exception('Вы не авторизованы');
            }
            $this->data->auth_hash = null;
            if (!$this->data->save(false)) {
                throw new Exception('Ошибка, обратитесь к системному администратору');
            }
            return $this->response(true);
        } catch (\Throwable $e) {
            return $this->response(null, $e);
        }
    }
    
    
    protected function getHashSum(array $hashData): string
    {
        return md5(implode([...array_values($hashData), getenv('APP_SALT')]));
    }
    
    protected function getHashData(UserModel $user): array
    {
        return [
            'username' => $user->username,
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'datetime' => date('Y-m-d H:i:s'),
        ];
    }
    
    protected function authHashEncode(array $data): string|false
    {
        try {
            if (empty($data)) {
                throw new Exception('data is empty');
            }
            $data['hash'] = $this->getHashSum($data);
            if (empty($data['hash'])) {
                throw new Exception('hash is empty');
            }
            return base64_encode(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable) {
            return false;
        }
    }
    
    protected function authHashDecode(?string $hash): array|false
    {
        try {
            if (!$hash) {
                throw new Exception('authHash not found');
            }
            $data = json_decode(base64_decode($hash), true);
            if (empty($dataHash = $data['hash'])) {
                throw new Exception('authHash is incorrect');
            }
            unset($data['hash']);
            if ($dataHash !== $this->getHashSum($data)) {
                throw new Exception('authHash is incorrect');
            }
            return (array)$data;
        } catch (\Throwable) {
            return false;
        }
    }
    
    
    protected function authHashGet(UserModel $user): bool|string
    {
        return $this->authHashEncode($this->getHashData($user));
    }
    
    protected function authHashCheck(UserModel $findUser, string $sessionAuthHash): bool
    {
        try {
            $dataValid = $this->getHashData($findUser);
            $dataCheck = $this->authHashDecode($sessionAuthHash);
            unset($dataValid['datetime'], $dataCheck['datetime']);
            foreach ($dataValid as $key => $value) {
                if ($dataCheck[$key] !== $value) {
                    throw new Exception("'$key' is incorrect");
                }
            }
            return true;
        } catch (\Throwable) {
            $this->authHashChange();
            return false;
        }
    }
    
    protected function authHashChange(?string $hash = null, bool $remember = false): void
    {
        $session = &$_SESSION[static::HASH_NAME];
        $cookie = &$_COOKIE[static::HASH_NAME];
        if (!empty($hash)) {
            $session = $hash;
            if ($remember) {
                setcookie(static::HASH_NAME, $hash, time() + 60 * 60 * 24 * 7, '/');
            } elseif (!empty($cookie) && $session !== $cookie) {
                setcookie(static::HASH_NAME, '', -1, '/');
            }
        } else {
            $session = null;
            if (!empty($cookie)) {
                setcookie(static::HASH_NAME, '', -1, '/');
            }
        }
    }
}