<?php

namespace Root\App\Models;

/**
 * @property ?int $id
 * @property string $username
 * @property string $password
 * @property int $group_id
 * @property string $auth_hash
 * @property string $email
 * @property string $birthday
 * @property string $created_at
 * @property string $updated_at
 *
 * @property GroupModel $group
 * @property string $groupName
 */
final class UserModel extends BaseModel
{
    const LIMIT_ROWS = 20;
    const DEFAULT_GROUP = 'user';
    
    /**
     * @throws \Exception
     */
    public function __construct(?array $props = [])
    {
        parent::__construct($props);
        $group = GroupModel::findByUnique($this->group_id);
        if (!$group) {
            $group = GroupModel::find(['name' => $this::DEFAULT_GROUP], true);
        }
        $this->fields['group'] = $group;
    }
    
    static protected function getTableName(): string
    {
        return 'users';
    }
    
    static protected function getUniqueField(): string
    {
        return 'id';
    }
    
    static protected function rules(): array
    {
        $passPattern = '#(?=.*\d+)(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[^\s\w])(^\S{8,20})#is';
        return [
            'username' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
            'password' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 8' => fn($value) => strlen($value) >= 8,
                'incorrect' => fn($value) => preg_match($passPattern, $value) !== false,
            ],
            'group_id' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
            ],
            'email' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
                // TODO ПОЧИНИТЬ ВАЛИДАЦИЮ
                'incorrect' => fn($value) => preg_match('#([^@]{2,})@([^.]{2,})\.(.{2,})#is', $value) !== false,
            ],
            'birthday' => [
                // 'incorrect' => fn($value) => preg_match('#[0-9]{2}-[0-9]{2}-[0-9]{4}#is', $value) !== false,
                'incorrect' => fn($value) => preg_match('#\d{4}-(\d{2}-?){2}#i', $value) !== false,
                'min date 01.01.1900' => fn($value) => strtotime($value) >= strtotime('1900-01-01'),
                'max date 31.12.2900' => fn($value) => strtotime($value) <= strtotime('2900-12-31'),
            ],
        ];
    }
    
    static protected function setters(): array
    {
        return [
            'password' => [
                fn($value) => password_hash($value, PASSWORD_BCRYPT),
            ],
        ];
    }
}