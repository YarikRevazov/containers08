<?php

require_once __DIR__ . '/testframework.php';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../site/modules/database.php';
require_once __DIR__ . '/../site/modules/page.php';

$tests = new TestFramework();

function testDbConnection() {
    global $config;
    try {
        $db = new Database($config["db"]["path"]);
        return assertExpression($db !== null, 'DB Connected');
    } catch (Exception $e) {
        return assertExpression(false, '', 'DB connection failed');
    }
}

function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count('page');
    return assertExpression(is_numeric($count), 'Count is numeric');
}

function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create('page', ['title' => 'Test Page', 'content' => 'Test Content']);
    return assertExpression($id > 0, 'Create succeeded');
}

function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create('page', ['title' => 'Read Test', 'content' => 'Reading content']);
    $data = $db->Read('page', $id);
    return assertExpression($data['title'] === 'Read Test', 'Read success');
}

function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create('page', ['title' => 'Update Me', 'content' => 'Old']);
    $db->Update('page', $id, ['title' => 'Updated', 'content' => 'New']);
    $data = $db->Read('page', $id);
    return assertExpression($data['title'] === 'Updated', 'Update success');
}

function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create('page', ['title' => 'To Delete', 'content' => 'Delete me']);
    $db->Delete('page', $id);
    $data = $db->Read('page', $id);
    return assertExpression($data === null, 'Delete success');
}

function testPageRender() {
    $template = __DIR__ . '/../site/templates/index.tpl';
    file_put_contents($template, "<h1>{title}</h1><p>{content}</p>");
    $page = new Page($template);
    $html = $page->Render(['title' => 'Hello', 'content' => 'World']);
    return assertExpression(strpos($html, '<h1>Hello</h1>') !== false, 'Page render works');
}

// Добавляем тесты
$tests->add('Database connection', 'testDbConnection');
$tests->add('Count method', 'testDbCount');
$tests->add('Create method', 'testDbCreate');
$tests->add('Read method', 'testDbRead');
$tests->add('Update method', 'testDbUpdate');
$tests->add('Delete method', 'testDbDelete');
$tests->add('Page render', 'testPageRender');

// Запускаем
$tests->run();
echo PHP_EOL . "🧪 Результат: " . $tests->getResult() . PHP_EOL;
 
