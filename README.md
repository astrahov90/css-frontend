<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Пример проекта на основе JQUERY+CSS и концепции MVC</h1>
    <br>
</p>

Данный проект построен основе фреймворка JQuery и CSS, а также использованием концепции MVC для бэкенда.

Портал представляет собой сат типа "Имидж-борд" с возможностью добавления постов, комментариев к постам,
а также учет лайков-дизлайков поста (рейтинг).


СТРУКТУРА ПРОЕКТА
-------------------

```
app
    controllers/         содержит контроллеры приложения
    core/                содержит базовые классы MVC
    db/                  содержит настройки СУБД и файл sqLite
    models/              содержит модели приложения
    views/               содержит виды web-приложения
    bootstrap.php        файл автозагрузки приложения
assets                   содержит ассеты js и css
files                    содержит загруженные пользователем данные
index.php                содержит точку входа в приложение
init.php                 содержит консольное приложение для инициализации базы sqLite
                         и ее первоначального заполнения
Dockerfile               содержит описание docker-образа
docker-compose.yml       содержит описание сборки docker-compose
    
```

БЫСТРЫЙ СТАРТ
-------------------

Для быстрого старта требуется клонировать репозиторий командой git clone.

Для запуска контейнера в корневой директории используйте команду docker compose up -d.

Сайт будет доступен по <a href="http://localhost:20080">ссылке</a>.

Для начального заполнения используются сервисы <a href="https://api.randomdatatools.ru"> randomDataTools </a>
(генерирование данных пользователей), <a href="https://api.multiavatar.com/"> multiAvatar </a>
(генерирование уникальных аватаров), <a href="https://fish-text.ru/api/"> Фиштекст </a>
(генерирование заголовков и текстов).

Пользователь администратора - admin 12346578.