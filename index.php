<?php

require_once __DIR__ . '/database/db.php';

$result = $db->query("
    SELECT * FROM news
    ORDER BY datetime(created_at) DESC
    LIMIT 6
");

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>شبكة سوريا نيوز 🇸🇾</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Tahoma, sans-serif;
    background: #f3f3f3;
    color: #111;
}

header {
    background: #050505;
    color: white;
    padding: 18px;
}

.header {
    max-width: 1150px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 25px;
    font-weight: bold;
}

nav a {
    color: white;
    text-decoration: none;
    margin-right: 18px;
}

.breaking {
    background: #b00000;
    color: white;
    padding: 12px;
    font-weight: bold;
}

.container {
    max-width: 1150px;
    margin: 35px auto;
    padding: 0 15px;
}

.title {
    font-size: 30px;
    margin-bottom: 25px;
    border-right: 5px solid #b00000;
    padding-right: 12px;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #ddd;
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.no-image {
    height: 200px;
    background: #222;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-content {
    padding: 18px;
}

.category {
    color: #b00000;
    font-size: 13px;
    font-weight: bold;
}

.card h2 {
    margin: 10px 0;
    line-height: 1.5;
}

.card p {
    color: #666;
    line-height: 1.7;
}

.read {
    display: inline-block;
    margin-top: 15px;
    color: #b00000;
    font-weight: bold;
    text-decoration: none;
}

.telegram {
    text-align: center;
    padding: 35px;
}

.telegram a {
    display: inline-block;
    background: #050505;
    color: white;
    padding: 13px 22px;
    border-radius: 8px;
    text-decoration: none;
}

footer {
    background: #050505;
    color: #aaa;
    text-align: center;
    padding: 25px;
}

@media(max-width:800px) {
    .header {
        flex-direction: column;
        gap: 15px;
    }

    .news-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<header>
    <div class="header">

        <div class="logo">
            شبكة سوريا نيوز 🇸🇾
        </div>

        <nav>
            <a href="index.php">الرئيسية</a>
            <a href="news.php">الأخبار</a>
            <a href="about.php">من نحن</a>
        </nav>

    </div>
</header>

<div class="breaking">
    🔴 عاجل | شبكة سوريا نيوز
</div>

<main class="container">

    <h1 class="title">
        آخر الأخبار
    </h1>

    <section class="news-grid">

    <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>

        <article class="card">

            <?php if (!empty($row['image'])): ?>

                <img src="<?= htmlspecialchars($row['image']) ?>" alt="صورة الخبر">

            <?php else: ?>

                <div class="no-image">
                    شبكة سوريا نيوز 🇸🇾
                </div>

            <?php endif; ?>

            <div class="card-content">

                <span class="category">
                    <?= htmlspecialchars($row['category']) ?>
                </span>

                <h2>
                    <?= htmlspecialchars($row['title']) ?>
                </h2>

                <p>
                    <?= htmlspecialchars(mb_substr(strip_tags($row['content']), 0, 150)) ?>...
                </p>

                <a class="read" href="article.php?id=<?= (int)$row['id'] ?>">
                    اقرأ الخبر ←
                </a>

            </div>

        </article>

    <?php endwhile; ?>

    </section>

</main>

<div class="telegram">

    <p>تابع الأخبار عبر قناة شبكة سوريا نيوز.</p>

    <br>

    <a href="https://t.me/suryainternetniuz" target="_blank">
        📢 قناة شبكة سوريا نيوز على تيليجرام
    </a>

</div>

<footer>
    © 2026 شبكة سوريا نيوز 🇸🇾
</footer>

</body>
</html>
