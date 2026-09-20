<?php

session_start();

require_once __DIR__ . '/../database/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare(
        "SELECT * FROM admins WHERE username = :username LIMIT 1"
    );

    $stmt->bindValue(':username', $username, SQLITE3_TEXT);

    $result = $stmt->execute();
    $admin = $result->fetchArray(SQLITE3_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {

        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header('Location: index.php');
        exit;

    } else {

        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';

    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>دخول الإدارة</title>

<style>

body {
    margin: 0;
    background: #111;
    font-family: Arial, Tahoma, sans-serif;
}

.box {
    width: 90%;
    max-width: 400px;
    background: white;
    margin: 100px auto;
    padding: 30px;
    border-radius: 12px;
}

input {
    width: 100%;
    padding: 13px;
    margin: 8px 0;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 13px;
    background: #b00000;
    color: white;
    border: 0;
    cursor: pointer;
}

.error {
    color: red;
    margin-bottom: 15px;
}

</style>

</head>

<body>

<div class="box">

<h2>لوحة إدارة شبكة سوريا نيوز</h2>

<?php if ($error): ?>

<p class="error">
<?= htmlspecialchars($error) ?>
</p>

<?php endif; ?>

<form method="POST">

<input
type="text"
name="username"
placeholder="اسم المستخدم"
required
>

<input
type="password"
name="password"
placeholder="كلمة المرور"
required
>

<button>
دخول
</button>

</form>

</div>

</body>

</html>
