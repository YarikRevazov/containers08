<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../site/config.php';
require_once __DIR__ . '/../site/modules/database.php'; 
require_once __DIR__ . '/../site/modules/page.php';

$tests = new TestFramework();

// Test 1: подключение к базе
$tests->add('Database connection', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    return assertExpression($db instanceof Database, "Database created", "Failed to create database");
});

// Test 2: Count
$tests->add('Count entries', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count >= 3, "Counted $count entries", "Expected at least 3 entries");
});

// Test 3: Create
$tests->add('Create entry', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create("page", ["title" => "Test Title", "content" => "Test Content"]);
    return assertExpression($id > 0, "Created entry with ID $id", "Failed to create entry");
});

// Test 4: Read
$tests->add('Read entry', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = $db->Read("page", 1);
    return assertExpression($data && isset($data['title']), "Read entry: " . $data['title'], "Failed to read entry");
});

// Test 5: Update
$tests->add('Update entry', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create("page", ["title" => "ToUpdate", "content" => "Before"]);
    $updated = $db->Update("page", $id, ["title" => "Updated", "content" => "After"]);
    $read = $db->Read("page", $id);
    return assertExpression($updated && $read['title'] === "Updated", "Entry updated", "Failed to update");
});

// Test 6: Delete
$tests->add('Delete entry', function () {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create("page", ["title" => "ToDelete", "content" => "Will be deleted"]);
    $deleted = $db->Delete("page", $id);
    $data = $db->Read("page", $id);
    return assertExpression($deleted && $data === false, "Entry deleted", "Failed to delete");
});

// Test 7: Page rendering
$tests->add('Page rendering', function () {
    $tplPath = __DIR__ . '/../templates/index.tpl';
    $page = new Page($tplPath);
    $html = $page->Render(["title" => "Test", "content" => "Some content"]);
    return assertExpression(strpos($html, "Test") !== false && strpos($html, "Some content") !== false, "Page rendered", "Page not rendered correctly");
});

// Запуск всех тестов
$tests->run();
echo $tests->getResult() . PHP_EOL;
