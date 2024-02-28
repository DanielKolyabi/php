<?php

namespace Root\App\Models;

use Exception;

// TODO вынести в Core
// TODO добавить проверку на корректность длины

final class BaseField
{
    private const TYPES = [
        'bool' => ['bool', 'boolean'],
        'int' => [
            ...['int', 'integer', 'tinyint', 'smallint', 'mediumint', 'bigint'], // int
            ...['year'], // date
        ],
        'float' => ['float'],
        'double' => ['double', 'double precision', 'dec', 'decimal', 'real'],
        'string' => [
            ...['char', 'varchar'], // char
            ...['text', 'tinytext', 'mediumtext', 'longtext'], // text
            ...['blob', 'tinyblob', 'mediumblob', 'longblob'], // blob
            ...['date', 'datetime', 'timestamp', 'time'], // date
        ],
    ];
    static private ?array $types = null;
    
    /**
     * @param string $sqlType
     * @return object{sql: string, php: string}
     */
    private function getTypeBySql(string $sqlType): object
    {
        $sqlType = strtolower($sqlType);
        
        $types = &self::$types;
        if ($types === null) {
            foreach (self::TYPES as $php => $arr) {
                foreach ($arr as $sql) {
                    $types[$sql] = $php;
                }
            }
        }
        
        $response = (object)[
            'sql' => $sqlType,
            'php' => 'string',
        ];
        
        if (array_key_exists($sqlType, $types)) {
            $response->php = $types[$sqlType];
        }
        
        return $response;
    }
    
    
    // Model
    private string $className;
    private string $fieldName;
    /** @var object{sql: string, php: string} */
    private object $type;
    private int $lengthMax;
    private mixed $value;
    private array $validation;
    private bool $isNullable;
    private bool $isPrimary;
    private bool $isAutoIncrement;
    private bool $isUnique;
    
    /**
     * @param string $className
     * @param array $typeData
     * <code>
     *     response.fetch => `show columns from {table}`
     * </code>
     * @param mixed $value
     * @param array $validation
     * <code>
     * [
     *     'require' => false,
     *     'errorMessage' => fn($value) => {{ logic }},
     *     ...
     * ]
     * </code>
     * @param bool $isUnique
     */
    public function __construct(
        string $className,
        array $typeData,
        mixed $value,
        array $validation = [
            // 'require' => true,
            // 'errorMessage' => fn($value) => {{ logic }},
        ],
        bool $isUnique = false
    ) {
        preg_match("#([^(]+)(\((.+)\))?#i", $typeData['Type'], $typeMatch);
        @[, $sqlType, , $length] = $typeMatch;
        
        $this->className = $className;
        $this->fieldName = $typeData['Field'];
        $this->type = $this->getTypeBySql($sqlType);
        $this->lengthMax = $length ?? 0;
        $this->value = $value;
        $this->validation = $validation;
        $this->isNullable = strtoupper($typeData['Null']) === 'YES';
        $this->isPrimary = strtoupper($typeData['Key']) === 'PRI';
        $this->isAutoIncrement = strtoupper($typeData['Extra']) === 'AUTO_INCREMENT';
        $this->isUnique = $isUnique || ($this->isPrimary && $this->isAutoIncrement);
        
        if ($this->value !== null) {
            settype($this->value, $this->type->php);
        } elseif (isset($typeData['Default']) && !in_array($this->type->sql, self::$dateTimeTypes)) {
            $this->value = $typeData['Default'];
            settype($this->value, $this->type->php);
        }
    }
    
    public function __debugInfo(): ?array
    {
        $typeSql = $this->type->sql;
        if ($this->lengthMax > 0) {
            $typeSql .= "($this->lengthMax)";
        }
        return [
            'info' => "{$this->className}->{$this->fieldName} {$typeSql}",
            'type' => $this->type->php,
            'value' => $this->value,
        ];
    }
    
    /**
     * @throws Exception
     */
    public function __invoke(...$attr)
    {
        @[$newValue, $validate] = [...$attr];
        if (@!isset($newValue)) {
            return $this->value;
        }
        $unique = $this->isPrimary && $this->isUnique;
        if ($unique && $newValue !== null) { // primary ai
            $this->error('is primary field: not editable');
        }
        if (!$unique && $newValue === null && !$this->isNullable) { // other !null
            $this->error('field can\'t be set to null');
        }
        if ($newValue !== null) {
            $this->changeType($newValue);
        }
        $this->value = $newValue;
        if (!!$validate) {
            $this->validate();
        }
        return $this;
    }
    
    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        $func = $this->validation;
        $isRequire = $func['require'] ?? false;
        unset($func['require']);
        if ($isRequire || !empty($this->value)) {
            foreach ($func as $errorMessage => $fn) {
                if (!$fn($this->value)) {
                    $this->error($errorMessage);
                }
            }
        }
        return true;
    }
    
    /**
     * @throws Exception
     */
    public function error($message): void
    {
        throw new Exception("Error {$this->className}->\${$this->fieldName} ($message)");
    }
    
    static private array $dateTimeTypes = ['date', 'datetime', 'timestamp', 'time', 'year'];
    private function changeType(mixed &$value): void
    {
        switch ($this->type->sql) {
            default:
                settype($value, $this->type->php);
                break;
            case '': // YYYY-MM-DD
                $value = date('Y-m-d', strtotime($value));
                break;
            case 'datetime': // YYYY-MM-DD hh:mm:ss
            case 'timestamp': // YYYY-MM-DD hh:mm:ss
                $value = date('Y-m-d H:i:s', strtotime($value));
                break;
            case 'time': // hh:mm:ss
                $value = date('H:i:s', strtotime($value));
                break;
            case 'year': // YYYY
                settype($value, 'int');
                break;
        }
    }
}