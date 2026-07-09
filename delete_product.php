<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: products.php?err=' . urlencode('No product selected.'));
    exit;
}

// Grab the name first so we can show a friendly confirmation message.
$stmt = mysqli_prepare($conn, 'SELECT name FROM products WHERE product_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$product) {
    header('Location: products.php?err=' . urlencode('Product not found.'));
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM products WHERE product_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: products.php?msg=' . urlencode("\"{$product['name']}\" was deleted."));
    exit;
} else {
    // Most likely cause: the product has related sales rows (FK RESTRICT).
    error_log('Delete product failed: ' . mysqli_stmt_error($stmt));
    header('Location: products.php?err=' . urlencode(
        "Could not delete \"{$product['name']}\" — it may already have recorded sales."));
    exit;
}
