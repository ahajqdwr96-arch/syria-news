<?php

require_once __DIR__ . '/database/db.php';

$result = $db->query("
    SELECT * FROM news
    ORDER BY datetime(created_at) DESC
");

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>الأخبار | شبكة سوريا نيوز</title>

<style>

body {
    margin: 0;
    font-family: Arial, Tahoma, sans-serif;
    background: #f3f3f3;
}

header {
    background: #050505;
    color: white;
    padding: 20px;
}

.header {
    max-width: 1100px;
    margin: auto;
    display: flex;
    justify-content: space-between;
}

nav a {
    color: white;
    text-decoration: none;
    margin-right: 15px;
}

.container {
    max-width: 1100px;
    margin: 35px auto;
    padding: 15px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
}

.card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #ddd;
}

.card img {
    width: 100%;
    height: 190px;
    object-fit: cover;
}

.no-image {
    height: 190px;
    background: #222;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content {
    padding: 18px;
}

.category {
    color: #b00000;
    font-weight: bold;
}

h2 {
    line-height: 1.5;
}

.read {
    color: #b00000;
    text-decoration: none;
    font-weight: bold;
}

@media(max-width:800px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .header {
        flex-direction: column;
        gap: 15px;
    }
}

</style>

</head>

<body>

<header>

<div class="header">

<strong>شبكة سوريا نيوز 🇸🇾</strong>

<nav>
<a href="index.php">الرئيسية</a>
<a href="news.php">الأخبار</a>
</nav>

</div>

</header>

<main class="container">

<h1>جميع الأخبار</h1>

<div class="grid">

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>

<div class="card">

<?php if ($row['image']): ?>

<img src="<?= htmlspecialchars($row['image']) ?>">

<?php else: ?>

<div class="no-image">
شبكة سوريا نيوز
</div>

<?php endif; ?>

<div class="content">

<div class="category">
<?= htmlspecialchars($row['category']) ?>
</div>

<h2>
<?= htmlspecialchars($row['title']) ?>
</h2>

<a class="read" href="article.php?id=<?= (int)$row['id'] ?>">
اقرأ الخبر ←
</a>

</div>

</div>

<?php endwhile; ?>

</div>

</main>

</body>
</html>
