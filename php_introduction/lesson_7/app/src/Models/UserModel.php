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
    
    /**
     * @return object{
     *     password: string,
     *     email: string,
     *     date: string,
     * }
     */
    static protected function regEx(): object
    {
        return (object)[
            'password' => '#(?=.*\d+)(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[^\s\w])(^\S{8,20})#i',
            'email' => '#[^@]{2,}@[^.]{2,}\.[\w^_]{2,}#i',
            'date' => '#\d{4}-(\d{2}-?){2}#i',
        ];
    }
    
    static protected function rules(): array
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
                'incorrect' => fn($value) => (bool)preg_match(self::regEx()->password, $value),
            ],
            'group_id' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
            ],
            'email' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
                'incorrect' => fn($value) => (bool)preg_match(self::regEx()->email, $value),
            ],
            'birthday' => [
                'incorrect' => fn($value) => (bool)preg_match(self::regEx()->date, $value),
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