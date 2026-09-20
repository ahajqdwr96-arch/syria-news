<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../database/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? 'أخبار سوريا');

    if ($title === '' || $content === '') {

        $message = 'يرجى كتابة عنوان الخبر ونصه.';

    } else {

        $imagePath = '';

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $mime = mime_content_type($_FILES['image']['tmp_name']);

            if (isset($allowed[$mime])) {

                $filename =
                    bin2hex(random_bytes(16)) .
                    '.' .
                    $allowed[$mime];

                $destination =
                    __DIR__ .
                    '/../uploads/' .
                    $filename;

                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $destination
                    )
                ) {

                    $imagePath = 'uploads/' . $filename;

                }
            }
        }

        $stmt = $db->prepare("
            INSERT INTO news
            (title, content, image, category)
            VALUES
            (:title, :content, :image, :category)
        ");

        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->bindValue(':image', $imagePath, SQLITE3_TEXT);
        $stmt->bindValue(':category', $category, SQLITE3_TEXT);

        $stmt->execute();

        $message = 'تم نشر الخبر بنجاح.';

    }
}

$news = $db->query("
    SELECT * FROM news
    ORDER BY datetime(created_at) DESC
");

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>لوحة الإدارة</title>

<style>

body {
    margin: 0;
    background: #f2f2f2;
    font-family: Arial, Tahoma, sans-serif;
}

header {
    background: #050505;
    color: white;
    padding: 18px;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 15px;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
}

input,
textarea,
select {
    width: 100%;
    padding: 13px;
    margin: 8px 0 15px;
    box-sizing: border-box;
}

textarea {
    min-height: 250px;
    resize: vertical;
}

button {
    background: #b00000;
    color: white;
    border: 0;
    padding: 13px 25px;
    cursor: pointer;
    border-radius: 6px;
}

.message {
    background: #e7f7e7;
    padding: 12px;
    margin-bottom: 15px;
}

.news-item {
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
}

</style>

</head>

<body>

<header>

<h2>
لوحة تحكم شبكة سوريا نيوز 🇸🇾
</h2>

<a
href="logout.php"
style="color:white;"
>
تسجيل الخروج
</a>

</header>

<main class="container">

<div class="box">

<h2>
نشر خبر جديد
</h2>

<?php if ($message): ?>

<div class="message">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form
method="POST"
enctype="multipart/form-data"
>

<label>
عنوان الخبر
</label>

<input
type="text"
name="title"
required
>

<label>
التصنيف
</label>

<select name="category">

<option>عاجل</option>
<option>أخبار سوريا</option>
<option>حلب</option>
<option>إدلب</option>
<option>درعا</option>
<option>سياسة</option>
<option>اقتصاد</option>
<option>أمن</option>

</select>

<label>
نص الخبر
</label>

<textarea
name="content"
required
></textarea>

<label>
صورة الخبر
</label>

<input
type="file"
name="image"
accept="image/jpeg,image/png,image/webp"
>

<button>
🚀 نشر الخبر
</button>

</form>

</div>

<div class="box">

<h2>
الأخبار المنشورة
</h2>

<?php while ($row = $news->fetchArray(SQLITE3_ASSOC)): ?>

<div class="news-item">

<strong>
<?= htmlspecialchars($row['title']) ?>
</strong>

<br>

<small>
<?= htmlspecialchars($row['category']) ?>
</small>

<br><br>

<a
href="../article.php?id=<?= (int)$row['id'] ?>"
target="_blank"
>
مشاهدة الخبر
</a>

|

<a
href="delete.php?id=<?= (int)$row['id'] ?>"
onclick="return confirm('هل تريد حذف الخبر؟');"
>
حذف
</a>

</div>

<?php endwhile; ?>

</div>

</main>

</body>

</html>
