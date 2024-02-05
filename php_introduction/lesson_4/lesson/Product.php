<?php

namespace src;

/*
Есть абстрактный товар.

Есть цифровой товар, штучный физический товар и товар на вес.

У каждого есть метод подсчёта финальной стоимости.

У цифрового товара стоимость постоянная и дешевле штучного товара в два раза, у штучного товара обычная стоимость, у весового – в зависимости от продаваемого количества в килограммах. У всех формируется в конечном итоге доход с продаж.

Что можно вынести в абстрактный класс, наследование?
*/

use Exception;

abstract class Product {
    private static array $data = [];
    public static function getIncome() :array {
        $income = ['all' => 0];
        foreach (self::$data as $groupKey => $groupData) {
            $income[$groupKey] = 0;
            /** @var Product $product */
            foreach ($groupData as $product) {
                $income[$groupKey] += $product->getCost();
            }
            $income['all'] += $income[$groupKey];
        }
        return $income;
    }

    const cVars = ['name', 'price', 'count'];

    protected string $name;
    protected float  $price;
    protected int    $count;

    /**
     * @param mixed ...$attr
     * <code>$attr = [
     * string $name,
     * float $price,
     * int $count
     * ]</code>
     * @throws Exception
     */
    public function __construct(...$attr) {
        $props = [];
        foreach (static::cVars as $key) {
            if (count($attr) === 0) {
                throw new Exception('Не все поля заполнены');
            }
            $props[$key] = array_shift($attr);
        }
        if (count($attr) > 0) {
            throw new Exception('Указаны лишние поля');
        }
        $this->updateProps($props);
        self::$data[static::class][] = $this;
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

    protected function updateProps(array $props) :void {
        foreach ($props as $key => $value) {
            if (method_exists($this, $methodName = 'set' . ucfirst($key))) {
                $this->$methodName($value);
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function setName(string $name) :void {
        if (strlen($name) < 2) {
            throw new Exception("Минимальная длина названия - 2 символа");
        }
        $this->name = $name;
    }
    /**
     * @param float $price
     * @throws Exception
     */
    public function setPrice(float $price) :void {
        if ($price < 1) {
            throw new Exception("Минимальная цена - 1₽");
        }
        $this->price = $price;
    }
    /**
     * @param int $count
     * @throws Exception
     */
    public function setCount(int $count) :void {
        if ($count < 1) {
            throw new Exception("Минимальное количество - 1 ед.");
        }
        $this->count = $count;
    }

    public abstract function getCost() :float;
    public function string() :string {
        $cost = $this->getCost();
        return "Name: $this->name; Price: $this->price; Count: $this->count; Cost: $cost";
    }
}

class PhysicalProduct extends Product {
    /**
     * @param string $name
     * @param float  $price
     * @param int    $count
     * @param mixed  ...$attr
     * @throws Exception
     */
    public function __construct(string $name, float $price, int $count, ...$attr) {
        parent::__construct($name, $price, $count, ...$attr);
    }

    public function getCost() :float {
        return $this->price * $this->count;
    }
}

class DigitalProduct extends PhysicalProduct {
    const cVars = [...parent::cVars, 'link'];

    protected string $link;

    /**
     * @param string $name
     * @param float  $price
     * @param string $link
     * @param mixed  ...$attr
     * @throws Exception
     */
    public function __construct(string $name, float $price, string $link, ...$attr) {
        parent::__construct($name, $price, 1, $link, ...$attr);
    }
}

class WeightProduct extends Product {
    const cVars = [...parent::cVars, 'weight'];

    protected float $weight;

    /**
     * @param string $name
     * @param float  $price
     * @param int    $priceForWeight
     * @param float  $weight
     * @param mixed  ...$attr
     * @throws Exception
     */
    public function __construct(string $name, float $price, int $priceForWeight, float $weight, ...$attr) {
        parent::__construct($name, $price, $priceForWeight, $weight, ...$attr);
    }

    public function getCost() :float {
        return round($this->price / $this->count * $this->weight, 2);
    }
}

try {
    $physical = new PhysicalProduct('Название', 15, 20);
    $digital  = new DigitalProduct('Название', 20, 'test.tt');
    $weight   = new WeightProduct('Название', 20, 1000, 157.28);

    print_r([
        'physical' => $physical->string(),
        'digital'  => $digital->string(),
        'weight'   => $weight->string(),
        'income'   => Product::getIncome(),
    ]);
} catch (Exception $e) {
    print_r(['Error' => $e->getMessage()]);
}