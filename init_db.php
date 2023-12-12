<?php
$db = new PDO('sqlite:todo.db');

// Create table
$db->exec("CREATE TABLE IF NOT EXISTS todos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    task TEXT,
    done INTEGER DEFAULT 0
)");
