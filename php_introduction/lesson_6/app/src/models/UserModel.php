<?php

namespace Root\App\models;

use Exception;
use PDO;
use Root\App\services\Database;
use Root\App\services\Helper;

/**
 * @property string $username
 * @property string $birthday
 */

/**
 * @property ?int $id_user
 * @property string $user_name
 * @property string $user_lastname
 * @property string $user_birthday_timestamp TODO вернуть на число/дату
 */
final class UserModel extends BaseModel
{
    static private bool $dataLoaded = false;
    /** @var UserModel[] */
    static private array $users = [];
    
    public static array $vars = [
        // 'username' => 'string',
        // 'birthday' => 'string',
        
        'id_user' => 'integer',
        'user_name' => 'string',
        'user_lastname' => 'string',
        // 'user_birthday_timestamp' => 'integer',
        'user_birthday_timestamp' => 'string', // TODO вернуть на число/дату
    ];
    
    protected function rules(): array
    {
        // return [
        //     'username' => [
        //         'empty' => fn($value) => !empty($value),
        //         'length < 2' => fn($value) => strlen($value) >= 2,
        //     ],
        //     'birthday' => [
        //         'empty' => fn($value) => !empty($value),
        //         'incorrect' => fn($value) => preg_match('#[0-9]{2}\-[0-9]{2}\-[0-9]{4}#is', $value) !== false,
        //         'min date 01.01.1900' => fn($value) => strtotime($value) >= strtotime('01-01-1900'),
        //         'max date 31.12.2900' => fn($value) => strtotime($value) <= strtotime('31-12-2900'),
        //     ],
        // ];
        return [
            'user_name' => [
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
            'user_lastname' => [
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
        ];
    }
    
    /**
     * @throws Exception
     */
    public function save(): void
    {
        $props = [];
        $error = '';
        $data = (array)$this;
        unset($data['id_user']);
        
        $keys = array_keys($data);
        foreach ($keys as $key => $value) {
            $keys[$key] = "`$value`";
        }
        $values = array_values($data);
        foreach ($values as $key => $value) {
            if (gettype($value) === 'string') {
                $values[$key] = "\"$value\"";
            }
        }
        
        if (!$this->id_user || !($user = self::findById($this->id_user))) {
            // create
            $keys = implode(', ', $keys);
            $values = implode(', ', $values);
            $sql = "insert into users($keys) values($values)";
            $error = 'Ошибка добавления пользователя';
        } else {
            // update
            $set = [];
            foreach ($values as $key => $value) {
                $set[] = "$keys[$key] = $value";
            }
            $set = implode(', ', $set);
            $sql = "update users set $set where id_user=:id";
            $props = ['id' => $this->id_user];
            $error = 'Ошибка обновления пользователя';
        }
        
        $handler = Database::app()->prepare($sql);
        if (!$handler->execute($props)) {
            throw new Exception($error);
        }
    }
    
    /**
     * @throws Exception
     */
    public function remove(): void
    {
        $handler = Database::app()->prepare('delete from users where id_user=:id');
        if (!$handler->execute(['id' => $this->id_user])) {
            throw new Exception('Пользователь не удален');
        }
    }
    
    /**
     * @param $username
     * @return UserModel|null
     */
    static public function findById($id = null): ?UserModel
    {
        $user = null;
        $handler = Database::app()->prepare('select * from users where id_user=:id');
        if ($handler->execute(['id' => $id])) {
            if ($item = $handler->fetch(PDO::FETCH_ASSOC)) {
                foreach ($item as $key => &$value) {
                    settype($value, self::$vars[$key]);
                }
                $user = new self($item);
            }
        }
        return $user;
    }
    
    /**
     * @return UserModel[]
     */
    static public function all(): array
    {
        $users = [];
        $handler = Database::app()->prepare('select * from users');
        if ($handler->execute()) {
            foreach ($handler->fetchAll(PDO::FETCH_ASSOC) as $item) {
                foreach ($item as $key => &$value) {
                    settype($value, self::$vars[$key]);
                }
                $users[] = new self($item);
            }
        }
        return $users;
    }
    
    /**
     * @param UserModel[] $users
     * @return void
     */
    /*
    static protected function saveData(array $users): void
    {
        if ($f = fopen(Helper::getStoragePath('users.json'), 'w+')) {
            $json = json_encode(
                $users,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
            );
            fwrite($f, $json);
            fclose($f);
        }
    }
    */
}