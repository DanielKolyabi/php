<?php

// 1. Реализовать основные 4 арифметические операции в виде функций с двумя параметрами – два параметра это числа.
//Обязательно использовать оператор return. Проверьте деление на ноль и верните текст, ошибка деления на ноль.
class Calc {
    function add($a, $b) {
        return $a + $b;
    }
    function sub($a, $b) {
        return $a - $b;
    }
    function multi($a, $b) {
        return $a * $b;
    }
    /**
     * @throws Exception
     */
    function div($a, $b) {
        try {
            return $b === 0 ? $this->error('На ноль делить нельзя!!!') : $a / $b;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * @throws Exception
     */
    function error($message) {
        throw new \Exception($message);
    }
    function mathOperation($arg1, $arg2, $operation) {
        switch ($operation) {
            case '+':
                return $this->add($arg1, $arg2);
            case '-':
                return $this->sub($arg1, $arg2);
            case '*':
                return $this->multi($arg1, $arg2);
            case '/':
                return $this->div($arg1, $arg2);
            default:
                return 'Операция не найдена';
        }
    }
}
$calc = new Calc();
print_r([
    'add' => $calc->add(20, 10),
    'sub' => $calc->sub(20, 10),
    'multi' => $calc->multi(20, 10),
    'div' => $calc->div(20, 10),
    'div0' => $calc->div(20, 0),
]);

// 2. Реализовать функцию с тремя параметрами: function mathOperation($arg1, $arg2, $operation),
//где $arg1, $arg2 – значения аргументов, $operation – строка с названием операции.
//В зависимости от переданного значения операции выполнить одну из арифметических операций
//(использовать функции из пункта 3) и вернуть полученное значение (использовать switch). Используйте функции из п.1
print_r([
    'mathOperation+' => $calc->mathOperation(20, 10, '+'),
    'mathOperation-' => $calc->mathOperation(20, 10, '-'),
    'mathOperation*' => $calc->mathOperation(20, 10, '*'),
    'mathOperation/' => $calc->mathOperation(20, 10, '/'),
    'mathOperation/0' => $calc->mathOperation(20, 0, '/'),
]);

// 3. Объявить массив, в котором в качестве ключей будут использоваться названия областей,
//а в качестве значений – массивы с названиями городов из соответствующей области.
//Вывести в цикле значения массива, чтобы результат был таким:
// Московская область: Москва, Зеленоград, Клин
// Ленинградская область: Санкт-Петербург, Всеволожск, Павловск, Кронштадт
// Рязанская область … (названия городов можно найти на maps.yandex.ru).
$arr = [
    'Московская область' => ['Москва', 'Зеленоград', 'Клин'],
    'Ленинградская область' => ['Санкт-Петербург', 'Всеволожск', 'Павловск', 'Кронштадт'],
    'Рязанская область' => ['Рязань'],
];
foreach ($arr as $key => $value) {
    print_r("$key: " . implode(', ', $value) . "\n");
}

// 4. Объявить массив, индексами которого являются буквы русского языка, а значениями – соответствующие
//латинские буквосочетания (‘а’=> ’a’, ‘б’ => ‘b’, ‘в’ => ‘v’, ‘г’ => ‘g’, …, ‘э’ => ‘e’, ‘ю’ => ‘yu’, ‘я’ => ‘ya’).
//Написать функцию транслитерации строк.
class Translit {
    protected const MAP = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'E', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH',
        'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
        'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
    ];
    static function convert(string $string) :string {
        return strtr($string, self::MAP);
    }
}
print_r(Translit::convert('Привет'));

// 5. *С помощью рекурсии организовать функцию возведения числа в степень. Формат: function power($val, $pow),
//где $val – заданное число, $pow – степень.
function power($val, $pow) {
    switch ($pow) {
        case 0:
            return 1;
        case 1:
            return $val;
        default:
            return $val * power($val, --$pow);
    }
};
print_r(['2^10' => power(2, 10)]);

// 6. *Написать функцию, которая вычисляет текущее время и возвращает его в формате с правильными склонениями, например:
// 22 часа 15 минут
// 21 час 43 минуты.

function dateStr($time = null) {
    $res = function ($val, $arr) {
        $v10 = $val % 10;
        if ($val >= 10 && $val <= 20) {
            return $arr[2];
        } else switch ($v10) {
            case 1:
                return $arr[0];
            case 2:
            case 3:
            case 4:
                return $arr[1];
            default:
                return $arr[2];
        }
    };

    $h = $time? date("H", strtotime($time)) : date("H");
    $i = $time? date("i", strtotime($time)) : date("i");

    $hours = "$h {$res($h, ['час', 'часа', 'часов'])}";
    $minutes = "$i {$res($i, ['минута', 'минуты', 'минут'])}";

    return"$hours $minutes";
}
print_r(['time' => dateStr()]);