# Практическая работа №06 — PHP, JSON и сессии

**Студент:** Солопов Антон  
**Группа:** ЭПБО-01-25  
**Дисциплина:** Технологии разработки программного обеспечения

## Результат
Приложение читает и изменяет dataset/notes.json, добавляет заметки с уникальными ID. dataset/users.json используется для выбора пользователя, имя хранится в PHP-сессии. Сценарии из session_scenarios.json используются при проверке работы сессии.

Обработка запросов находится в public/index.php и src/NotesController.php. Работа с JSON выполняется в src/NoteRepository.php. Шаблоны расположены в templates/. Функция render() передаёт данные представлению. После POST выполняется redirect HTTP 303. Для неизвестного значения page возвращается HTTP 404. Отсутствующий и повреждённый notes.json обрабатывается с выводом сообщения.

## Запуск
PHP 8.1+. Папка dataset должна быть доступна PHP для записи в notes.json.

```bash
php -S localhost:8000 -t public
```

Откройте http://localhost:8000/?page=notes.

## Проверка
```bash
php -d zend.assertions=1 -d assert.exception=1 tests/repository_test.php
```

Проверьте сценарии из session_scenarios.json в обычном и приватном окне браузера: выбор пользователя, сохранение имени в сессии и очистку сессии.

## Структура
public/index.php — точка входа и маршрутизация.  
src/NotesController.php — обработка запросов.  
src/NoteRepository.php — чтение и запись JSON.  
src/helpers.php — загрузка JSON, экранирование и подключение шаблона.  
templates/ — представления.  
dataset/ — исходные данные.  
tests/ — проверки.
