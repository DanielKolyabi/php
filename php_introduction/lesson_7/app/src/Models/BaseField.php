<?php

namespace Root\App\Models;

use Exception;

// TODO добавить проверку на возможность установки NULL
// TODO добавить автоформат даты/времени

final class BaseField
{
    public function __construct(
        protected string $fullFieldName,
        protected string $type,
        protected mixed $value,
        protected int $length,
        protected array $validationFunctions = [
            // 'errorMessage' => fn($value) => {{ logic }},
        ],
        protected bool $isUnique = false,
        protected bool $isNull = true,
    ) {
        if ($this->value !== null) {
            settype($this->value, $this->type);
        }
    }
    
    /**
     * @throws Exception
     */
    public function __invoke(...$attr)
    {
        @[$newValue, $validate] = [...$attr];
        if (isset($newValue)) {
            if (!empty($newValue)) {
                if ($this->isUnique) {
                    $this->error('is primary field: not editable');
                }
                settype($newValue, $this->type);
            }
            $this->value = $newValue;
            if (!!$validate) {
                $this->validate();
            }
            return $this;
        }
        return $this->value;
    }
    
    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        $func = $this->validationFunctions;
        $isRequire = $func['require'] ?? true;
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
        throw new Exception("Error $this->fullFieldName ($message)");
    }
}