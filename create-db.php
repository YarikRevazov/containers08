<?php

$schemaFile = __DIR__ . '/sql/schema.sql';
$databaseFile = __DIR__ . '/db/db.sqlite';

// Проверка, существует ли файл schema.sql
if (!file_exists($schemaFile)) {
    exit("Файл schema.sql не найден!" . PHP_EOL);
}

// Подключение к SQLite
try {
    $pdo = new PDO('sqlite:' . $databaseFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Выполнение SQL
    $schema = file_get_contents($schemaFile);
    $pdo->exec($schema);

    echo "✅ База данных успешно создана: db/db.sqlite" . PHP_EOL;
} catch (PDOException $e) {
    echo "❌ Ошибка при создании базы данных: " . $e->getMessage() . PHP_EOL;
}
