<?php
require_once __DIR__ . '/config.php';

$dbPath = $config["db"]["path"];

if (!file_exists($dbPath)) {
    $pdo = new PDO("sqlite:" . $dbPath);
    $sql = file_get_contents(__DIR__ . '/../sql/schema.sql');
    $pdo->exec($sql);
    echo "✅ База данных создана!";
} else {
    echo "ℹ️ База данных уже существует.";
}
