<?php

namespace Root\App\Models;

/**
 * @property ?int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
final class GroupModel extends BaseModel
{
    const LIMIT_ROWS = 20;
    
    static protected function getTableName(): string
    {
        return 'groups';
    }
    
    static protected function getUniqueField(): string
    {
        return 'id';
    }
    
    static protected function rules(): array
    {
        return [
            'name' => [
                'require' => true,
                'empty' => fn($value) => !empty($value),
                'length < 2' => fn($value) => strlen($value) >= 2,
            ],
        ];
    }
}