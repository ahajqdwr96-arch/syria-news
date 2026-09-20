<?php

require_once __DIR__ . '/database/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM news WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

$result = $stmt->execute();
$news = $result->fetchArray(SQLITE3_ASSOC);

if (!$news) {
    http_response_code(404);
    die("الخبر غير موجود.");
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= htmlspecialchars($news['title']) ?> | شبكة سوريا نيوز</title>

<style>

body {
    margin: 0;
    background: #f3f3f3;
    font-family: Arial, Tahoma, sans-serif;
}

header {
    background: #050505;
    color: white;
    padding: 20px;
}

.header {
    max-width: 900px;
    margin: auto;
}

.container {
    max-width: 900px;
    margin: 35px auto;
    padding: 15px;
}

.article {
    background: white;
    padding: 25px;
    border-radius: 12px;
}

.category {
    color: #b00000;
    font-weight: bold;
}

h1 {
    line-height: 1.6;
}

.date {
    color: #777;
    margin-bottom: 20px;
}

.article-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 25px;
}

.content {
    font-size: 19px;
    line-height: 2;
    white-space: pre-line;
}

.back {
    display: inline-block;
    margin-top: 30px;
    color: #b00000;
    font-weight: bold;
    text-decoration: none;
}

</style>

</head>

<body>

<header>

<div class="header">
شبكة سوريا نيوز 🇸🇾
</div>

</header>

<main class="container">

<article class="article">

<div class="category">
<?= htmlspecialchars($news['category']) ?>
</div>

<h1>
<?= htmlspecialchars($news['title']) ?>
</h1>

<div class="date">
<?= htmlspecialchars($news['created_at']) ?>
</div>

<?php if ($news['image']): ?>

<img class="article-image"
src="<?= htmlspecialchars($news['image']) ?>"
alt="صورة الخبر">

<?php endif; ?>

<div class="content">
<?= htmlspecialchars($news['content']) ?>
</div>

<a class="back" href="news.php">
← العودة إلى الأخبار
</a>

</article>

</main>

</body>
</html>
