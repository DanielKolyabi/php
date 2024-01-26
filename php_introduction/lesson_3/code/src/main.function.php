<?php
function main(string $configFileAddress) :string {
    if (!($config = readConfig($configFileAddress))) {
        return handleError("Невозможно подключить файл настроек");
    }
    if (!($functionName = parseCommand()) || !function_exists($functionName)) {
        return handleError("Вызываемая функция не существует");
    }
    return $functionName($config);
}

function parseCommand() :?string {
    if (!($arg = $_SERVER['argv'][1])) {
        return 'helpFunction';
    }
    return match ($arg) {
        'read-all'      => 'readAllFunction',
        'add'           => 'addFunction',
        'clear'         => 'clearFunction',
        'search'        => 'searchBD',
        'delete'        => 'deleteUser',
        'read-profiles' => 'readProfilesDirectory',
        'read-profile'  => 'readProfile',
        default         => null,
    };
}