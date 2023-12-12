<?php
require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


$db = new PDO('sqlite:todo.db');

$isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Create a log channel
$log = new Logger('todo');
$log->pushHandler(new StreamHandler('./test.log'));



session_start();

// Initialize the todo list
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}
// Add a new todo item

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['new_todo'])) {
    $stmt = $db->prepare("INSERT INTO todos (task) VALUES (:task)");
    $stmt->execute([':task' => $_POST['new_todo']]);
    $log->info('New task added: ' . $_POST['new_todo']);

    if ($isAjaxRequest) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit; // End the script execution for AJAX requests
    }
}


// Toggle the done status
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['toggle'])) {
    $stmt = $db->prepare("UPDATE todos SET done = NOT done WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
}

// Remove a todo item
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove'])) {
    $stmt = $db->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->execute([':id' => $_GET['remove']]);
}


$todos = $db->query("SELECT * FROM todos ORDER BY done, id DESC");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List</title>
    <link href="style.css" rel="stylesheet">
    <script src="public/index.js"></script>

</head>

<body>
    <h1>Simple To-Do List</h1>
    <form id="todo-form">
        <input type="text" id="new_todo" name="new_todo" required>
        <button type="submit">Add</button>
    </form>
    <div id="todo-list">
        <?php foreach ($todos as $todo) : ?>
            <div class="todo-item">
                <span class="<?= $todo['done'] ? 'strikethrough' : '' ?>">
                    <?= htmlspecialchars($todo['task']) ?>
                </span>
                <a href="?toggle=<?= $todo['id'] ?>">[<?= $todo['done'] ? 'Uncheck' : 'Check' ?>]</a>
                <a href="?remove=<?= $todo['id'] ?>">[Remove]</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>