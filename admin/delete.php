<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../database/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare(
    "SELECT image FROM news WHERE id = :id"
);

$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row) {

    if (!empty($row['image'])) {

        $file = __DIR__ . '/../' . $row['image'];

        if (is_file($file)) {
            unlink($file);
        }
    }

    $delete = $db->prepare(
        "DELETE FROM news WHERE id = :id"
    );

    $delete->bindValue(':id', $id, SQLITE3_INTEGER);
    $delete->execute();
}

header('Location: index.php');
exit;
