<?php
header('Content-Type: text/xml; charset=utf-8');
$pdo = new PDO('pgsql:host=db;dbname=postgres', 'postgres', 'postgres');

// Категории
$sections = $pdo->query('SELECT id, title, parent_id FROM sections')->fetchAll(PDO::FETCH_ASSOC);

// Товары
$products = $pdo->query('SELECT * FROM products')->fetchAll(PDO::FETCH_ASSOC);

// Офферы
$offers = $pdo->query('SELECT * FROM offers')->fetchAll(PDO::FETCH_ASSOC);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<yml_catalog date="' . date('Y-m-d H:i') . '">';
echo '<shop>';
echo '<name>Интернет-магазин</name>';
echo '<company>Интернет-магазин</company>';

// Категории
echo '<categories>';
foreach ($sections as $cat) {
    echo '<category id="' . $cat['id'] . '"';
    if ($cat['parent_id']) echo ' parentId="' . $cat['parent_id'] . '"';
    echo '>' . htmlspecialchars($cat['title']) . '</category>';
}
echo '</categories>';

// Офферы
echo '<offers>';
foreach ($offers as $offer) {
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] == $offer['product_id']) {
            $product = $p;
            break;
        }
    }
    if (!$product) continue;

    echo '<offer id="' . $offer['id'] . '" productId="' . $offer['product_id'] . '" quantity="10">';
    if ($offer['image']) {
        echo '<picture>' . htmlspecialchars($offer['image']) . '</picture>';
    }
    echo '<price>' . $offer['price'] . '</price>';
    echo '<categoryId>' . $product['category_id'] . '</categoryId>';
    echo '<name>' . htmlspecialchars($offer['title']) . '</name>';
    echo '<xmlId>' . $offer['id'] . '</xmlId>';
    echo '<productName>' . htmlspecialchars($product['name']) . '</productName>';
    if ($offer['color']) {
        echo '<param name="Цвет" code="color">' . htmlspecialchars($offer['color']) . '</param>';
    }
    if ($offer['discount']) {
        echo '<param name="Скидка" code="discount">' . $offer['discount'] . '</param>';
    }
    if ($product['brand']) {
        echo '<vendor>' . htmlspecialchars($product['brand']) . '</vendor>';
    }
    if ($product['description']) {
        echo '<param name="Описание" code="description">' . htmlspecialchars($product['description']) . '</param>';
    }
    echo '</offer>';
}
echo '</offers>';

echo '</shop>';
echo '</yml_catalog>';