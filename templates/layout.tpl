<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$page_title|escape} — {$app_name|escape}</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a href="/" class="logo">{$app_name|escape}</a>
            <nav class="nav">
                <a href="/">Главная</a>
            </nav>
        </div>
    </header>

    <main class="container">
        {block name="content"}{/block}
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; {$smarty.now|date_format:"%Y"} {$app_name|escape}</p>
        </div>
    </footer>
</body>
</html>
