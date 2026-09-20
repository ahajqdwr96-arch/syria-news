<?php

$db = new SQLite3(__DIR__ . '/news.sqlite');

$db->exec("
CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    image TEXT,
    category TEXT DEFAULT 'أخبار سوريا',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);
");

$check = $db->querySingle("SELECT COUNT(*) FROM admins");

if ($check == 0) {
    $username = 'admin';
    $password = password_hash('ChangeMe123!', PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        "INSERT INTO admins (username, password) VALUES (:username, :password)"
    );

    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->execute();
}

echo "تم تجهيز قاعدة البيانات بنجاح.";
