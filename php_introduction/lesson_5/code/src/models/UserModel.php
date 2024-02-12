<?php

namespace app\models;

use app\services\Helper;

/**
 * @property string $username
 * @property string $birthday
 */
final class UserModel extends BaseModel
{
    static private bool $dataLoaded = false;
    /** @var UserModel[] */
    static private array $users = [];

    public static array $vars = [
        'username' => 'string',
        'birthday' => 'string',
    ];

    protected function rules(): array
    {
        return [
            'username' => [
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
            'birthday' => [
                'empty' => fn($value) => !empty($value),
                'incorrect' => fn($value) => preg_match('#[0-9]{2}\-[0-9]{2}\-[0-9]{4}#is', $value) !== false,
                'min date 01.01.1900' => fn($value) => strtotime($value) >= strtotime('01-01-1900'),
                'max date 31.12.2900' => fn($value) => strtotime($value) <= strtotime('31-12-2900'),
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function save(): void
    {
        self::add($this);
    }

    /**
     * @return UserModel[]
     */
    static public function all(): array
    {
        $users = [];
        if (file_exists($address = Helper::getPathStorage() . '/users.json')) {
            foreach (json_decode(file_get_contents($address), true) as $data) {
                $users[] = new UserModel((array)$data);
            }
        }
        return $users;
    }

    /**
     * @param $username
     * @return UserModel|null
     */
    static public function findByUsername($username): ?UserModel
    {
        $users = self::all();
        $idx = array_search($username, array_column($users, 'username'));
        return $idx !== false ? $users[$idx] : null;
    }

    /**
     * @param UserModel $newUser
     * @return void
     * @throws \Exception
     */
    static public function add(UserModel $newUser): void
    {
        $users = self::all();
        if (in_array($newUser, $users)) {
            throw new \Exception('User exist!');
        }
        $users[] = $newUser;
        if ($f = fopen(Helper::getPathStorage() . '/users.json', 'w+')) {
            $json = json_encode(
                $users,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
            );
            fwrite($f, $json);
            fclose($f);
        }
    }
}