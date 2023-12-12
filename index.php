<?php
require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
    array_unshift($_SESSION['todos'], ['task' => $_POST['new_todo'], 'done' => false]);
    $log->info('New task added: ' . $_POST['new_todo']);



    if ($isAjaxRequest) {
        echo json_encode(['success' => true]);
        exit; // End the script execution for AJAX requests
    }
}

// Toggle the done status
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['toggle'])) {
    $index = $_GET['toggle'];
    $_SESSION['todos'][$index]['done'] = !$_SESSION['todos'][$index]['done'];
    usort($_SESSION['todos'], function ($a, $b) {
        return $b['done'] <=> $a['done'];
    });
}

// Remove a todo item
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove'])) {
    $index = $_GET['remove'];
    array_splice($_SESSION['todos'], $index, 1);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List</title>
    <link href="style.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var form = document.getElementById('todo-form');
            var todoList = document.getElementById('todo-list'); // Add this ID to your todo list container in HTML

            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting the traditional way


                var xhr = new XMLHttpRequest();
                var todoText = document.getElementById('new_todo').value;

                xhr.open('POST', 'index.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Set the AJAX header

                xhr.onload = function() {
                    // Process our return data
                    if (xhr.status >= 200 && xhr.status < 300) {
                        // This will run when the request is successful
                        console.log('success!', xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Append the new todo item to the list

                            var newTodoItem = document.createElement('div');
                            newTodoItem.className = 'todo-item';
                            newTodoItem.innerHTML = `
                                <span>${todoText}</span>
                                <a href="?toggle=0">[Check]</a>
                                <a href="?remove=0">[Remove]</a>
                            `;

                            // Append the new todo item to the list
                            todoList.appendChild(newTodoItem);

                            // Clear the input field
                            document.getElementById('new_todo').value = '';
                        }
                    } else {
                        // This will run when it's not successful
                        console.log('The request failed!');
                    }
                };

                xhr.send('new_todo=' + encodeURIComponent(todoText));
                // Clear the input field after sending
                document.getElementById('new_todo').value = '';
            });
        });
    </script>

</head>

<body>
    <h1>Simple To-Do List</h1>
    <form id="todo-form">
        <input type="text" id="new_todo" name="new_todo" required>
        <button type="submit">Add</button>
    </form>
    <div id="todo-list">
        <?php foreach ($_SESSION['todos'] as $index => $todo) : ?>
            <div class="todo-item">
                <span class="<?= $todo['done'] ? 'strikethrough' : '' ?>">
                    <?= htmlspecialchars($todo['task']) ?>
                </span>
                <a href="?toggle=<?= $index ?>">[<?= $todo['done'] ? 'Uncheck' : 'Check' ?>]</a>
                <a href="?remove=<?= $index ?>">[Remove]</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>