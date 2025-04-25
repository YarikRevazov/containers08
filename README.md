# Лабораторная работа №8: Непрерывная интеграция с использованием GitHub Actions

## Цель работы

Научиться настраивать непрерывную интеграцию (CI) с помощью **GitHub Actions** для автоматического тестирования и сборки web-приложения на базе PHP с использованием контейнеров.

## Задание

- Создать веб-приложение на базе PHP.
- Написать для него юнит-тесты.
- Настроить систему CI с использованием GitHub Actions.
- Выполнить тесты в контейнере и запускать их автоматически при каждом обновлении репозитория.

## Выполнение работы

### Шаг 1: Создание репозитория и структуры проекта

- Создан репозиторий `containers08` на GitHub и клонирован локально.
- Структура проекта:

```
containers08/
├── site/
│   ├── modules/
│   │   ├── database.php
│   │   └── page.php
│   ├── templates/
│   │   └── index.tpl
│   ├── styles/
│   │   └── style.css
│   ├── config.php
│   └── index.php
├── sql/
│   └── schema.sql
├── tests/
│   ├── testframework.php
│   └── tests.php
├── Dockerfile
└── .github/
    └── workflows/
        └── main.yml
```

### Шаг 2: База данных

Файл `sql/schema.sql` содержит структуру таблицы `page` и несколько записей:

```sql
CREATE TABLE page (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  content TEXT
);

INSERT INTO page (title, content) VALUES ('Page 1', 'Content 1');
INSERT INTO page (title, content) VALUES ('Page 2', 'Content 2');
INSERT INTO page (title, content) VALUES ('Page 3', 'Content 3');
```

### Шаг 3: Web-приложение

Пример кода `index.php`:

```php
<?php

require_once __DIR__ . '/modules/database.php';
require_once __DIR__ . '/modules/page.php';
require_once __DIR__ . '/config.php';

$db = new Database($config["db"]["path"]);
$page = new Page(__DIR__ . '/templates/index.tpl');

$pageId = $_GET['page'];
$data = $db->Read("page", $pageId);
echo $page->Render($data);
```

### Шаг 4: Написание тестов

Создан простой фреймворк `testframework.php`:

```php
class TestFramework {
    private $tests = [];
    private $success = 0;

    public function add($name, $test) {
        $this->tests[$name] = $test;
    }

    public function run() {
        foreach ($this->tests as $name => $test) {
            echo "Running test {$name}\n";
            if ($test()) {
                $this->success++;
            }
            echo "End test {$name}\n";
        }
    }

    public function getResult() {
        return "{$this->success} / " . count($this->tests);
    }
}
```

В `tests.php` добавлены тесты для методов класса `Database`.

### Шаг 5: Dockerfile

Пример `Dockerfile`:

```Dockerfile
FROM php:7.4-fpm as base

RUN apt-get update && \
    apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo_sqlite

VOLUME ["/var/www/db"]

COPY sql/schema.sql /var/www/db/schema.sql

RUN echo "prepare database" && \
    cat /var/www/db/schema.sql | sqlite3 /var/www/db/db.sqlite && \
    chmod 777 /var/www/db/db.sqlite && \
    rm -rf /var/www/db/schema.sql && \
    echo "database is ready"

COPY site /var/www/html
```

### Шаг 6: GitHub Actions

Файл `.github/workflows/main.yml`:

```yaml
name: CI

on:
  push:
    branches:
      - main

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Build the Docker image
        run: docker build -t containers08 .

      - name: Create `container`
        run: docker create --name container --volume database:/var/www/db containers08

      - name: Copy tests to the container
        run: docker cp ./tests container:/var/www/html

      - name: Up the container
        run: docker start container

      - name: Run tests
        run: docker exec container php /var/www/html/tests/tests.php

      - name: Stop the container
        run: docker stop container

      - name: Remove the container
        run: docker rm container
```

### Шаг 7: Запуск и проверка

- Команды для коммита и отправки на GitHub:

```bash
git add .
git commit -m "Добавлены все файлы для работы"
git push origin main
```

- Во вкладке **Actions** на GitHub можно увидеть, как CI проходит успешно.

---

## Ответы на вопросы

1. **Что такое непрерывная интеграция?**  
   Непрерывная интеграция (CI) — это процесс автоматического тестирования и сборки проекта при каждом изменении кода, что позволяет быстро обнаруживать ошибки.

2. **Для чего нужны юнит-тесты? Как часто их нужно запускать?**  
   Юнит-тесты проверяют работу отдельных функций и классов. Их рекомендуется запускать при каждом изменении кода.

3. **Что нужно изменить в `.github/workflows/main.yml`, чтобы тесты запускались при Pull Request?**

```yaml
on:
  push:
    branches:
      - main
  pull_request:
    branches:
      - main
```

4. **Как удалить созданные Docker-образы после выполнения тестов?**

Добавить в конце workflow:

```yaml
- name: Remove Docker images
  run: docker rmi containers08
```

---

## Выводы

Настройка CI с помощью **GitHub Actions** позволяет автоматизировать тестирование и повысить надёжность веб-приложения. Использование Docker обеспечивает одинаковую среду выполнения как в разработке, так и в CI, упрощая отладку и развертывание.

