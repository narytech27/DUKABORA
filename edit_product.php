<?php
require 'config.php';

$id = intval($_GET['id'] ?? $_POST['product_id'] ?? 0);
if ($id <= 0) {
    header('Location: products.php?err=' . urlencode('No product selected.'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---------- Module 5: server-side validation ----------
    $name        = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $supplier_id = intval($_POST['supplier_id'] ?? 0);
    $price       = floatval($_POST['price'] ?? 0);
    $stock_qty   = intval($_POST['stock_qty'] ?? -1);

    if ($name === '')       { $errors[] = 'Product name is required.'; }
    if ($category_id <= 0)  { $errors[] = 'Please choose a category.'; }
    if ($supplier_id <= 0)  { $errors[] = 'Please choose a supplier.'; }
    if ($price <= 0)        { $errors[] = 'Price must be a positive number.'; }
    if ($stock_qty < 0)     { $errors[] = 'Stock quantity cannot be negative.'; }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            'UPDATE products SET name = ?, category_id = ?, supplier_id = ?, price = ?, stock_qty = ? WHERE product_id = ?');
        mysqli_stmt_bind_param($stmt, 'siidii', $name, $category_id, $supplier_id, $price, $stock_qty, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: products.php?msg=' . urlencode("\"$name\" was updated."));
            exit;
        } else {
            error_log('Update product failed: ' . mysqli_stmt_error($stmt));
            $errors[] = 'An error occurred. Please try again.';
        }
    }
    // fall through and re-render the form with the submitted (invalid) values
    $product = compact('name', 'category_id', 'supplier_id', 'price', 'stock_qty');
} else {
    // load existing record
    $stmt = mysqli_prepare($conn, 'SELECT * FROM products WHERE product_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$product) {
        header('Location: products.php?err=' . urlencode('Product not found.'));
        exit;
    }
}

$page_title = 'Edit Product';
$active = 'products';
include 'includes/header.php';

$categories = safe_query($conn, 'SELECT category_id, category_name FROM categories ORDER BY category_name');
$suppliers  = safe_query($conn, 'SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name');
?>

<div class="panel">
    <h2>Edit Product</h2>

    <?php foreach ($errors as $e): ?>
        <p class="alert alert-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form class="ledger-form" method="POST" action="edit_product.php?id=<?php echo $id; ?>">
        <input type="hidden" name="product_id" value="<?php echo $id; ?>">

        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $c['category_id']; ?>" <?php echo ($product['category_id'] == $c['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="supplier_id">Supplier</label>
        <select id="supplier_id" name="supplier_id" required>
            <?php mysqli_data_seek($suppliers, 0); while ($s = mysqli_fetch_assoc($suppliers)): ?>
                <option value="<?php echo $s['supplier_id']; ?>" <?php echo ($product['supplier_id'] == $s['supplier_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['supplier_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="price">Price (TZS)</label>
        <input type="number" step="0.01" min="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="stock_qty">Stock Quantity</label>
        <input type="number" step="1" min="0" id="stock_qty" name="stock_qty" value="<?php echo htmlspecialchars($product['stock_qty']); ?>" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a class="btn btn-edit" href="products.php">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
