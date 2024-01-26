<?php
function readAllFunction(array $config) :string {
    if (!($address = $config['storage']['address']) || !file_exists($address) || !is_readable($address)) {
        return handleError("Файл не существует");
    }
    $arr      = json_decode(file_get_contents($address), true);
    $contents = '';
    foreach ($arr as $item) {
        $contents .= sprintf("%s, %s", $item['name'], $item['birthday']) . PHP_EOL;
    }
    return $contents;
}

function addFunction(array $config) :string {
    $address = $config['storage']['address'];

    $name = readline("Введите имя: ");
    if (!validateName($name)) {
        return handleError("Некорректное имя");
    }

    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");
    if (!validateDate($date)) {
        return handleError("Некорректный формат даты");
    }

    $arr = json_decode(file_get_contents($address), true) ?: [];
    $arr[] = [
        "name"     => $name,
        "birthday" => $date,
    ];

    $fileHandler = fopen($address, 'w');
    if ($fileHandler === false) {
        return handleError("Не удалось открыть файл для записи");
    }

    $jsonEncodedData = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (fwrite($fileHandler, $jsonEncodedData) === false) {
        fclose($fileHandler);
        return handleError("Произошла ошибка записи. Данные не сохранены");
    }

    fclose($fileHandler);
    return "Запись добавлена в файл $address";
}

function clearFunction(array $config) :string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");

        fwrite($file, '');

        fclose($file);
        return "Файл очищен";
    } else {
        return handleError("Файл не существует");
    }
}

function helpFunction() :string {
    return handleHelp();
}

function readConfig(string $configAddress) :array|false {
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config) :string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!is_dir($profilesDirectoryAddress)) {
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if (count($files) > 2) {
        foreach ($files as $file) {
            if (in_array($file, ['.', '..']))
                continue;

            $result .= $file . "\r\n";
        }
    } else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config) :string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if (!isset($_SERVER['argv'][2])) {
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if (!file_exists($profileFileName)) {
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson  = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}

function searchBD(array $config) :string {
    $address = $config['storage']['address'];
    $date    = date('d-m');
    $result  = null;

    if (is_readable($address)) {
        $file = file_get_contents($address);
        $arr  = json_decode($file, true);

        if ($file) {
            foreach ($arr as $user) {
                if (substr($user['birthday'], 0, 5) === $date) {
                    $age    = date('Y') - substr($user['birthday'], 6, 4);
                    $result .= sprintf("Сегодня у %s %d-й день рождения!", $user['name'], $age) . PHP_EOL;
                }
            }
        }
    } else {
        return handleError('Файл не существует или не доступен для чтения');
    }

    return $result ?? 'Сегодня дней рождения нет';
}

function deleteUser(array $config) :string {
    $address = $config['storage']['address'];
    $name    = readline("Введите имя: ");

    if (is_readable($address)) {
        $fileContents = file_get_contents($address);
        $arr          = json_decode($fileContents, true) ?: [];

        $result    = [];
        $userFound = false;

        foreach ($arr as $user) {
            if ($user['name'] === $name) {
                $userFound = true;
                continue;
            }
            $result[] = $user;
        }

        if (!$userFound) {
            return handleError("Пользователь не найден");
        }

        $jsonEncodedData = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $file = fopen($address, 'w');
        if ($file === false) {
            return handleError("Не удалось открыть файл для записи");
        }

        if (fwrite($file, $jsonEncodedData) === false) {
            fclose($file);
            return handleError("Произошла ошибка записи. Данные не сохранены");
        }

        fclose($file);
        return "Пользователь удален";
    }

    return handleError("Файл не доступен для чтения");
}