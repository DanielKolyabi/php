<?php

namespace Root\App\Models;

use Exception;
use PDO;
use Root\App\services\Database;
use Root\App\Services\Helper;

abstract class BaseModel
{
    // Settings
    public const LIMIT_ROWS = 100;
    
    static abstract protected function getTableName(): string; // ex: "users"
    
    static abstract protected function getUniqueField(): string; // ex: "id"
    
    protected function rules(): array
    {
        return [
            // 'fieldName' => [
            //     '{{ errorMessage }}' => fn($value) => {{ logic }},
            // ],
        ];
    }
    
    
    // Model
    protected array $fields = [];
    
    /**
     * @throws Exception
     */
    public function __construct(array $props = [])
    {
        try {
            $className = static::getClassName();
            $columns = $this->getColumns();
            // foreach ($columns as $key => $type) {
            foreach ($columns as $key => $data) {
                // $this->fields[$key] = new BaseField(
                //     "$className->\$$key",
                //     "$type",
                //     $props[$key] ?? null,
                //     $this->rules()[$key] ?? [],
                //     $key === static::getUniqueField()
                // );
                $data['value'] = $props[$key] ?? null;
                $this->fields[$key] = new BaseField(...array_values($data));
            }
            // echo '<pre>';
            // print_r($this->fields);
            // echo '</pre>';
            // die;
        } catch (\Throwable) {
            //
        }
    }
    
    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        $this->checkFieldExist($name);
        return $this->fields[$name]();
    }
    
    /**
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        $this->checkFieldExist($name);
        $this->fields[$name]($value, true);
    }
    
    /**
     * @throws Exception
     */
    public function __invoke(): array
    {
        $data = [];
        foreach (array_keys($this->getColumns()) as $column) {
            $data[$column] = $this->$column;
        }
        return $data;
    }
    
    /**
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    private function checkFieldExist(string $fieldName): void
    {
        if (!isset($this->fields[$fieldName])) {
            $this->error('field not exist', $fieldName);
        }
    }
    
    /**
     * @throws Exception
     */
    private function error(string $errorMessage, string $fieldName = null)
    {
        $className = static::getClassName();
        $fullName = !empty($fieldName) ? "$className->\$$fieldName" : $className;
        throw new Exception("Error $fullName (field not exist)");
    }
    
    /**
     * @throws Exception
     */
    public function exist(): bool
    {
        $table = static::getTable();
        $unique = static::getUnique();
        if (!empty($value = $this->$unique)) {
            $handler = Database::app()->prepare("select count($unique) from $table where $unique=:value");
            $handler->execute(['value' => $value]);
            return (bool)$handler->fetch()[0];
        }
        return false;
    }
    
    /**
     * @throws Exception
     */
    public function save(): bool
    {
        return !$this->exist() ? $this->create(false) : $this->update(true);
    }
    
    /**
     * @throws Exception
     */
    public function create($exist = null): bool
    {
        if ($exist === null) {
            $exist = $this->exist();
        }
        if ($exist) {
            $this->error('record exist');
        }
        $table = static::getTable();
        $unique = static::getUnique();
        $data = [];
        foreach ($this() as $key => $value) {
            if (in_array($key, [$unique, 'created_at', 'updated_at']) && empty($value)) {
                continue;
            }
            $this->fields[$key]->validate();
            $data["`$key`"] = gettype($value) === 'string' ? "\"$value\"" : ($value === null ? 'null' : $value);
        }
        $keys = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $handler = Database::app()->prepare("insert into $table($keys) values($values)");
        // echo '<pre>';
        // print_r($handler);
        // echo '</pre>';
        // die;
        return $handler->execute();
    }
    
    /**
     * @throws Exception
     */
    public function update($exist = null): bool
    {
        if ($exist === null) {
            $exist = $this->exist();
        }
        if (!$exist) {
            $this->error('record not exist');
        }
        $table = static::getTable();
        $unique = static::getUnique();
        if (empty($value = $this->$unique)) {
            $this->error('empty field', $unique);
        }
        $data = [];
        foreach ($this() as $dataKey => $dataValue) {
            if ($dataKey === $unique || (in_array($dataKey, ['created_at', 'updated_at']) && empty($value))) {
                continue;
            }
            $this->fields[$dataKey]->validate();
            $data[] = "`$dataKey` = " . (gettype($dataValue) === 'string' ? "\"$dataValue\"" : $dataValue);
        }
        $data = implode(', ', $data);
        $handler = Database::app()->prepare("update $table set $data where $unique=:value");
        return $handler->execute(['value' => $value]);
    }
    
    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        $table = static::getTable();
        $unique = static::getUnique();
        if (empty($value = $this->$unique)) {
            $this->error('empty field', $unique);
        }
        $handler = Database::app()->prepare("delete from $table where $unique=:value");
        return $handler->execute(['value' => $value]);
    }
    
    /**
     * @param mixed $value
     * @return static|null
     * @throws Exception
     */
    static public function findByUnique(mixed $value): ?static
    {
        $table = static::getTable();
        $unique = static::getUnique();
        $handler = Database::app()->prepare("select * from $table where $unique=:value");
        if ($handler->execute(['value' => $value]) && is_array($data = $handler->fetch(PDO::FETCH_ASSOC))) {
            return new static($data);
        }
        return null;
    }
    
    /**
     * @param string $filedName
     * @param mixed $value
     * @return static[]
     * @throws Exception
     */
    static public function findBy(string $filedName, mixed $value): array
    {
        $table = static::getTable();
        $rows = [];
        $handler = Database::app()->prepare("select * from $table where $filedName=:value");
        if ($handler->execute(['value' => $value])) {
            $items = $handler->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($items)) {
                foreach ($items as $data) {
                    $rows[] = new static($data);
                }
            }
        }
        return $rows;
    }
    
    /**
     * @param int $page
     * @param int $limit
     * @return static[]
     * @throws Exception
     */
    static public function getAll(int $page = 0): array
    {
        $table = static::getTable();
        $rows = [];
        $limitStart = $page * static::LIMIT_ROWS;
        $limitEnd = $limitStart + static::LIMIT_ROWS;
        $handler = Database::app()->prepare("select * from $table limit $limitStart, $limitEnd");
        if ($handler->execute()) {
            $items = $handler->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($items)) {
                foreach ($items as $data) {
                    $rows[] = new static($data);
                }
            }
        }
        return $rows;
    }
    
    
    // Service
    static protected ?array $columns = null;
    static protected ?string $uniqueField = null;
    static private array $typeMap = [
        'bool' => [ // boolean
            'BOOL',
            'BOOLEAN',
        ],
        'int' => [ // integer
            'INT',
            'INTEGER',
            'TINYINT',
            'SMALLINT',
            'MEDIUMINT',
            'BIGINT',
        ],
        'float' => [
            'FLOAT'
        ],
        'double' => [
            'DOUBLE',
            'DOUBLE PRECISION',
            'DECIMAL',
            'DEC',
        ],
        'string' => [
            'CHAR',
            'VARCHAR',
            'TEXT',
            'TINYTEXT',
            'MEDIUMTEXT',
            'LONGTEXT',
            'DATE',
            'DATETIME',
            'TIMESTAMP',
            'TIME',
            'YEAR',
        ],
        'array' => [],
        'object' => [],
        'null' => [],
    ];
    static private ?array $typeMapSqlToPhp = null;
    
    static public function getClassName(): string
    {
        return (string)preg_replace(
            "#" . addslashes(Helper::getRootNamespace()) . "\\\#i",
            '',
            get_called_class()
        );
    }
    
    /**
     * @throws Exception
     */
    static public function getTable(): string
    {
        if (!($table = static::getTableName())) {
            $className = static::getClassName();
            throw new Exception("Error $className (table not specified)");
        }
        return $table;
    }
    
    /**
     * @throws Exception
     */
    static public function getUnique(): string
    {
        if (!($unique = static::getUniqueField() ?? static::$uniqueField)) {
            $className = static::getClassName();
            throw new Exception("Error $className (unique field not specified)");
        }
        return $unique;
    }
    
    /**
     * @return array
     * @throws Exception
     */
    private function getColumns(): array
    {
        $fields = &static::$columns;
        if ($fields === null) {
            // set map: sqlType => phpType
            $types = &self::$typeMapSqlToPhp;
            if ($types === null) {
                foreach (self::$typeMap as $phpType => $sqlTypes) {
                    foreach ($sqlTypes as $sqlType) {
                        $types[strtolower($sqlType)] = strtolower($phpType);
                    }
                }
            }
            
            // set fields
            $table = static::getTable();
            $handler = Database::app()->prepare("show columns from " . $table);
            if ($handler->execute()) {
                $uniqueField = &static::$uniqueField;
                $fields = [];
                foreach ($handler->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    preg_match("#([^\(]+)(\((.+)\))?#i", $item['Type'], $typeData);
                    // [$typeName, $length] = explode(
                    //     ',',
                    //     preg_replace(
                    //         "#(.*)\((.*)\)#i",
                    //         "$1,$2",
                    //         $item['Type']
                    //     )
                    // );
                    @[,$typeName,,$length] = $typeData;
                    // echo '<pre>ITEM:';
                    // print_r([
                    //     'item' => $item,
                    //     'type' => $typeName,
                    //     'typeData' => $typeData,
                    // ]);
                    // echo '</pre>';
                    if ($type = $types[strtolower($typeName)] ?? null) {
                        // if ($item['Key'] === 'PRI' && $item['Extra'] = 'auto_increment' && $uniqueField === null) {
                        //     $uniqueField = $item['Field'];
                        // }
                        // $fields[$item['Field']] = $type;
                        $fieldName = $item['Field'];
                        $isPrimary = $item['Key'] === 'PRI';
                        $isNull = $item['Null'] === 'YES';
                        if ($isPrimary && $item['Extra'] = 'auto_increment' && $uniqueField === null) {
                            $uniqueField = $item['Field'];
                        }
                        $fields[$fieldName] = [
                            'fullName' => static::getClassName() . "->\$$fieldName",
                            'type' => $type,
                            'value' => null,
                            'length' => $length ?? 0,
                            'rules' => static::rules()[$fieldName] ?? [],
                            'isPrimary' => $isPrimary,
                            'isNull' => $isNull,
                        ];
                    }
                }
            }
        }
        return $fields;
    }
}