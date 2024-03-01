<?php

namespace Root\App\Models;

use Exception;
use PDO;
use Root\App\Services\Database;
use Root\App\Services\Helper;

// TODO вынести в Core

/**
 * Базовая модель
 *
 * Добавление связей:
 * <code>
 *     $this->fields['propsName'] = {ModelName}::findByUnique($this->{fieldName});
 * </code>
 */
abstract class BaseModel
{
    // Settings
    public const LIMIT_ROWS = 100;
    
    /**
     * Название таблицы в БД
     * @return string
     */
    static abstract protected function getTableName(): string; // ex: "users"
    
    /**
     * Название уникального поля (идентификатор)
     * @return string
     */
    static abstract protected function getUniqueField(): string; // ex: "id"
    
    /**
     * Правила валидации
     * @return array
     */
    static protected function rules(): array
    {
        return [
            // 'fieldName' => [
            //     '{{ errorMessage }}' => fn($value) => '{{ logic }}',
            //     '{{ errorMessage }}' => fn($value) => (bool)preg_match('#{{ pattern }}#i', $value),
            // ],
        ];
    }
    
    /**
     * Правила преобразования при установке значения
     * @return array
     */
    static protected function setters(): array
    {
        return [
            // 'fieldName' => [
            //     fn($value) => {{ result }},
            // ],
        ];
    }
    
    /**
     * Преобразование значения
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    static public function setter(string $name, mixed $value): mixed
    {
        if (array_key_exists($name, static::setters())) {
            foreach (static::setters()[$name] as $fn) {
                $value = $fn($value);
            }
        }
        return $value;
    }
    
    
    // Model
    protected array $fields = [];
    
    /**
     * @param ?array $props
     */
    public function __construct(?array $props = [])
    {
        try {
            $className = $this::getClassName();
            $columns = $this::getColumns();
            $unique = $this::getUnique();
            if (!$props) {
                $props = [];
            }
            foreach ($columns as $name => $typeData) {
                $this->fields[$name] = new BaseField(
                    $className,
                    $typeData,
                    $props[$name] ?? null,
                    $this::rules()[$name] ?? [],
                    $unique,
                );
            }
        } catch (\Throwable) {
            //
        }
    }
    
    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        $this->checkFieldExist($name, 'get');
        $fieldData = $this->fields[$name];
        return $fieldData instanceof BaseField ? $fieldData() : $fieldData;
    }
    
    /**
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        $this->checkFieldExist($name, 'set');
        $this->fields[$name]($value, true);
        // setters
        if (array_key_exists($name, $this::setters())) {
            foreach ($this::setters()[$name] as $fn) {
                $this->fields[$name]($fn($value), false);
            }
        }
    }
    
    /**
     * @throws Exception
     */
    public function __invoke(): array
    {
        $data = [];
        foreach (array_keys($this::getColumns()) as $column) {
            $data[$column] = $this->$column;
        }
        return $data;
    }
    
    /**
     * @throws Exception
     */
    public function __debugInfo(): ?array
    {
        $data = [];
        foreach (array_keys($this->fields) as $column) {
            $data[$column] = $this->$column;
        }
        return $data;
    }
    
    /**
     * @param string $fieldName
     * @param string $type
     * @return void
     * @throws Exception
     */
    private function checkFieldExist(string $fieldName, string $type): void
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->error('field not exist', $fieldName);
        }
        if ($type === 'set' && !array_key_exists($fieldName, $this::getColumns())) {
            $this->error('field not writable', $fieldName);
        }
    }
    
    /**
     * @throws Exception
     */
    private function error(string $errorMessage, string $fieldName = null)
    {
        $className = $this::getClassName();
        $fullName = !empty($fieldName) ? "$className->\$$fieldName" : $className;
        throw new Exception("Error $fullName ($errorMessage)");
    }
    
    /**
     * @throws Exception
     */
    public function exist(): bool
    {
        $table = $this::getTable();
        $unique = $this::getUnique();
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
        $table = $this::getTable();
        $unique = $this::getUnique();
        $data = [];
        foreach ($this() as $dataKey => $dataValue) {
            if (in_array($dataKey, [$unique, 'created_at', 'updated_at']) && empty($dataValue)) {
                continue;
            }
            try {
                $this->checkFieldExist($dataKey, 'set');
            } catch (\Throwable) {
                continue;
            }
            $this->fields[$dataKey]->validate();
            if (gettype($dataValue) === 'string') {
                $dataValue = "\"$dataValue\"";
            } else if ($dataValue === null) {
                $dataValue = 'null';
            }
            $data["`$dataKey`"] = $dataValue;
        }
        $keys = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $handler = Database::app()->prepare("insert into $table($keys) values($values)");
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
        $table = $this::getTable();
        $unique = $this::getUnique();
        if (empty($value = $this->$unique)) {
            $this->error('empty field', $unique);
        }
        $data = [];
        foreach ($this() as $dataKey => $dataValue) {
            if ($dataKey === $unique || (in_array($dataKey, ['created_at', 'updated_at']) && empty($value))) {
                continue;
            }
            try {
                $this->checkFieldExist($dataKey, 'set');
            } catch (\Throwable) {
                continue;
            }
            $this->fields[$dataKey]->validate();
            if (gettype($dataValue) === 'string') {
                $dataValue = "\"$dataValue\"";
            } else if ($dataValue === null) {
                $dataValue = 'null';
            }
            $data[] = "`$dataKey` = $dataValue";
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
        $table = $this::getTable();
        $unique = $this::getUnique();
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
     * @param array $fields
     * @param bool $once
     * @return static[]|static|null
     * @throws Exception
     */
    static public function find(array $fields, bool $once = false): array|static|null
    {
        $table = static::getTable();
        
        $where = [];
        $props = [];
        foreach ($fields as $key => $value) {
            $where[$key] = "$key=:$key";
            $props[$key] = $value;
        }
        $where = implode(' && ', $where);
        
        $rows = [];
        $handler = Database::app()->prepare("select * from $table where $where");
        if ($handler->execute($props)) {
            $items = $handler->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($items)) {
                foreach ($items as $data) {
                    $rows[] = new static($data);
                }
            }
        }
        return $once ? ($rows[0] ?? null) : $rows;
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
    static private array $columns = [];
    
    static private function getClassName(): string
    {
        return (string)preg_replace(
            "#" . addslashes(Helper::getRootNamespace()) . "\\\#i",
            '',
            static::class
        );
    }
    
    /**
     * @throws Exception
     */
    static private function getTable(): string
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
    static private function getUnique(): string
    {
        $unique = static::getUniqueField();
        if (!$unique) {
            $className = static::getClassName();
            throw new Exception("Error $className (unique field not specified)");
        }
        return $unique;
    }
    
    /**
     * @return array
     * @throws Exception
     */
    static private function getColumns(): array
    {
        @$fields = &self::$columns[static::class];
        if (empty($fields)) {
            $fields = [];
            $table = static::getTable();
            $handler = Database::app()->prepare("show columns from " . $table);
            if ($handler->execute()) {
                foreach ($handler->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $fieldName = $item['Field'];
                    $fields[$fieldName] = $item;
                }
            }
        }
        return $fields;
    }
}