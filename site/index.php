<?php

// Подключаем классы
require_once __DIR__ . '/modules/database.php';
require_once __DIR__ . '/modules/page.php';
require_once __DIR__ . '/config.php';

// Создаём подключение к базе данных
$db = new Database($config["db"]["path"]);

// Загружаем шаблон
$page = new Page(__DIR__ . '/templates/index.tpl');

// Получаем ID страницы из URL, по умолчанию 1
$pageId = $_GET['page'] ?? 1;

// Получаем данные из базы
$data = $db->Read("page", $pageId);

// Отображаем результат
echo $page->Render($data);
