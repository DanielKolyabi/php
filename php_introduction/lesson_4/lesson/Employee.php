<?php

namespace src;

use Exception;

/*
Сделайте класс Сотрудник, в котором будут следующие private поля:
name (имя),
age (возраст),
salary (зарплата)

и следующие public методы:
setName,
getName,
setAge,
getAge,
setSalary,
getSalary.

Создайте 2 объекта этого класса: 'Олег', возраст 25, зарплата 1000 и 'Мария', возраст 26, зарплата 2000.

Выведите на экран сумму зарплат Олега и Марии. Выведите на экран более старшего сотрудника из двух.
*/

/**
 * @method string getName
 * @method int getAge
 * @method float getSalary
 */
class Employee {
    /** @var Employee[] */
    public static array $employers = [];

    public static function getData() :array {
        $data = [];
        foreach (static::$employers as $key => $item) {
            $data[$key] = $item->string();
        }
        return $data;
    }
    public static function sumSalary(Employee ...$employees) :float {
        $data = count($employees) > 0 ? $employees : static::$employers;
        return (float)array_sum(array_column($data, 'salary'));
    }
    public static function sortAge(Employee ...$employees) :array {
        if (count($employees) > 0) {
            $e = $employees;
        } else {
            $e = &static::$employers;
        }
        usort($e, fn($a, $b) => $b->age - $a->age);
        return $e;
    }

    // Object
    protected string $name;
    protected int    $age;
    protected float  $salary;

    /**
     * @param string $name
     * @param int    $age
     * @param float  $salary
     * @throws Exception
     */
    public function __construct(string $name, int $age, float $salary) {
        $args = ['name', 'age', 'salary'];
        foreach ($args as $key) {
            $methodName = 'set' . ucfirst($key);
            $this->$methodName($$key);
        }
        static::$employers[] = $this;
    }
    public function __get(string $name) :mixed {
        return property_exists($this, $name) && isset($this->$name) ? $this->$name : null;
    }
    public function __set(string $name, $value) :void {
        $methodName = 'set' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            $this->$methodName($value);
        }
    }
    public function __call(string $name, array $arguments) :mixed {
        if (str_starts_with($name, 'get')) {
            $propertyName = lcfirst(substr($name, 3));
            return $this->$propertyName;
        }
        return null;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function setName(string $name) :void {
        if (strlen($name) < 2) {
            throw new Exception("Минимальная длина имени 2 символа");
        }
        $this->name = $name;
    }
    /**
     * @param int $age
     * @throws Exception
     */
    public function setAge(int $age) :void {
        if ($age < 18) {
            throw new Exception("Минимальный возраст - 18 лет");
        }
        $this->age = $age;
    }
    /**
     * @param float $salary
     * @throws Exception
     */
    public function setSalary(float $salary) :void {
        if ($salary < 1000) {
            throw new Exception("Что это за работа с зарплатой $salary?)");
        }
        $this->salary = $salary;
    }

    public function diff(Employee $e) :string {
        $diff = $this->salary - $e->salary;
        if ($diff == 0) {
            return 'Ваша зарплата равна';
        } else {
            $r = 'больше';
            if (!($s = $diff > 0)) {
                $r    = 'меньше';
                $diff *= -1;
            }
            return "Ваша зарплата на $diff $r";
        }
    }
    public function string() :string {
        return implode(' ', [
            $this->name,
            $this->age,
            $this->salary,
        ]);
    }
}

$emp = Employee::class;

try {
    new $emp('Иван', 23, 50000);
    new $emp('Дмитрий', 37, 70000);
    new $emp('Олег', 29, 65000);
} catch (Exception $e) {
    print_r(['Ошибка' => $e->getMessage()]);
}

Employee::sortAge();
$e = &$emp::$employers;
print_r([
    'emp'                   => $emp::getData(),
    'emp0->getAge()'        => $e[0]->getAge(),
    'emp0->diff(emp1)'      => $e[0]->diff($e[1]),
    'Salary sum emp0, emp2' => $emp::sumSalary($e[0], $e[2]),
    'Older emp0, emp2'      => $emp::sortAge($e[0], $e[2])[0]->string(),
    'Salary sum all'        => $emp::sumSalary(),
]);