<?php

namespace app\models;

use Exception;

abstract class BaseModel
{
    public static array $vars = [
        // 'fieldName' => 'type',
    ];

    protected function rules(): array
    {
        return [
            // 'fieldName' => [
            //     'errorMessage' => fn($value) => {{ logic }},
            // ],
        ];
    }

    public function __construct(array $props)
    {
        foreach ($props as $key => $value) {
            $this->$key = $value;
        }
        foreach (static::$vars as $key => $type) {
            if (!isset($this->$key)) {
                $this->$key = null;
            }
        }
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        if ($this->validation($name, $value)) {
            $this->$name = $value;
        }
    }

    /**
     * @throws Exception
     */
    protected function validation($fieldName, $value): bool
    {
        $type = array_key_exists($fieldName, static::$vars) ? static::$vars[$fieldName] : null;
        $rules = array_key_exists($fieldName, $this->rules()) ? $this->rules()[$fieldName] : [];
        try {
            if (empty($type)) {
                $this->getFieldException($fieldName, "field not exist");
            } else if ($value !== null && gettype($value) !== $type) {
                $this->getFieldException($fieldName, "type not $type");
            } else if (!empty($rules)) {
                foreach ($rules as $errorMessage => $func) {
                    if (!$func($value)) {
                        $this->getFieldException($fieldName, $errorMessage);
                    }
                }
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    /**
     * @throws Exception
     */
    private function getFieldException($fieldName, $message)
    {
        throw new Exception("Error " . get_called_class() . "->\$$fieldName ($message)");
    }
}