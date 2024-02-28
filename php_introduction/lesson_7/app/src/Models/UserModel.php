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
    
    public function __construct(array $props)
    {
        parent::__construct($props);
        $this->fields['group'] = GroupModel::findByUnique($this->group_id);
    }
    
    static protected function getTableName(): string
    {
        return 'users';
    }
    
    static protected function getUniqueField(): string
    {
        return 'id';
    }
    
    protected function rules(): array
    {
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
            ],
            'group_id' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
            ],
            'email' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
                'incorrect' => fn($value) => preg_match('#([^@]){2}+@([^.]){2}+\.(.*){2}+#is', $value) !== false,
            ],
            'birthday' => [
                'incorrect' => fn($value) => preg_match('#[0-9]{2}-[0-9]{2}-[0-9]{4}#is', $value) !== false,
                'min date 01.01.1900' => fn($value) => strtotime($value) >= strtotime('01-01-1900'),
                'max date 31.12.2900' => fn($value) => strtotime($value) <= strtotime('31-12-2900'),
            ],
        ];
    }
    
    protected function setters(): array
    {
        return [
            'password' => [
                fn($value) => md5($value),
            ],
        ];
    }
}