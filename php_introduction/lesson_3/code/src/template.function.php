<?php
function handleError(string $errorText) :string {
    return "\033[31m" . $errorText . " \r\n \033[97m";
}

function handleHelp() :string {
    $help = [
        [
            'Программа работы с файловым хранилищем',
            'Порядок вызова',
        ],
        [
            'php /code/app.php [COMMAND]',
        ],
        [
            'Доступные команды:',
            'read-all - чтение всего файла',
            'add - добавление записи',
            'clear - очистка файла',
            'delete - удалить пользователя',
            'search - найти ближайший день рождения',
            'read-profiles - вывести список профилей пользователей',
            'read-profile - вывести профиль выбранного пользователя',
            'help - помощь',
        ],
    ];

    $response = [];
    foreach ($help as $value) {
        $response[] = implode("\n", $value);
    }
    return implode("\n\n", $response);
}