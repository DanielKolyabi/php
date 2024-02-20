### DockerRun

```sh
# Сборка проекта
sh build.sh lesson6 && exit
```

```bash
# Запуск проекта (тестовый, при выходе остановит)
sh start.sh lesson6 application && exit
```

[go to site](http://mysite.local:81)

### Д/З

Мы стали работать с исключениями. Создайте в Render [логику обработки исключений](app/src/App.php) так, чтобы она встраивалась в общий
шаблон. Вызов будет выглядеть примерно так:

```php
try{
    $app = new Application();
    echo $app->run();
}
catch(Exception $e){
    echo Render::renderExceptionPage($e);
}
```

- Создайте метод [обновления пользователя](app/src/controllers/UsersController.php) новыми данными. Например, `/user/update/?id=42&name=Петр`\
  Такой вызов обновит имя у пользователя с ID 42. Обратите внимание, что остальные поля не меняются. Также помните, что
  пользователя с ID 42 может и не быть в базе.
- Создайте метод [удаления пользователя](app/src/controllers/UsersController.php) из базы. Учитывайте, что пользователя может не быть в базе `/user/delete/?id=42`