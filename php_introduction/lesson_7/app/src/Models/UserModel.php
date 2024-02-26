<?php

namespace Root\App\Models;

use Exception;

// /**
//  * @property ?int $id_user
//  * @property string $user_name
//  * @property string $user_lastname
//  * @property string $user_birthday_timestamp TODO вернуть на число/дату
//  */

/**
 * @property ?int $id
 * @property string $username
 * @property string $password
 * @property string $auth_hash
 * @property string $email
 * @property string $datetime
 * @property string $created_at
 */
final class UserModel extends BaseModel
{
    const LIMIT_ROWS = 20;
    
    static protected function getTableName(): string
    {
        return 'users';
    }
    
    static protected function getUniqueField(): string
    {
        // return 'id_user';
        return 'id';
    }
    
    protected function rules(): array
    {
        return [
            'username' => [
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
            'password' => [
                'empty' => fn($value) => !empty($value),
                'length < 8' => fn($value) => strlen($value) >= 8,
            ],
            'email' => [
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
                'incorrect' => fn($value) => preg_match('#([^@]){2}+@([^\.]){2}+\.(.*){2}+#is', $value) !== false,
            ],
            'birthday' => [
                'require' => false,
                'incorrect' => fn($value) => preg_match('#[0-9]{2}\-[0-9]{2}\-[0-9]{4}#is', $value) !== false,
                'min date 01.01.1900' => fn($value) => strtotime($value) >= strtotime('01-01-1900'),
                'max date 31.12.2900' => fn($value) => strtotime($value) <= strtotime('31-12-2900'),
            ],
        ];
        // return [
        //     'user_name' => [
        //         'empty' => fn($value) => !empty($value),
        //         'length < 2' => fn($value) => strlen($value) >= 2,
        //     ],
        //     'user_lastname' => [
        //         'empty' => fn($value) => !empty($value),
        //         'length < 2' => fn($value) => strlen($value) >= 2,
        //     ],
        // ];
    }
    
    // /**
    //  * @param UserModel[] $users
    //  * @return void
    //  */
    // static protected function saveData(array $users): void
    // {
    //     if ($f = fopen(Helper::getStoragePath('users.json'), 'w+')) {
    //         $json = json_encode(
    //             $users,
    //             JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
    //         );
    //         fwrite($f, $json);
    //         fclose($f);
    //     }
    // }
}