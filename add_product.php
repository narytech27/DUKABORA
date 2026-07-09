<?php
require 'config.php';

$errors = [];
$name = $category_id = $supplier_id = $price = $stock_qty = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---------- Module 5: server-side validation ----------
    $name        = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $supplier_id = intval($_POST['supplier_id'] ?? 0);
    $price       = floatval($_POST['price'] ?? 0);
    $stock_qty   = intval($_POST['stock_qty'] ?? -1);

    if ($name === '')                 { $errors[] = 'Product name is required.'; }
    if ($category_id <= 0)            { $errors[] = 'Please choose a category.'; }
    if ($supplier_id <= 0)             { $errors[] = 'Please choose a supplier.'; }
    if ($price <= 0)                  { $errors[] = 'Price must be a positive number.'; }
    if ($stock_qty < 0)               { $errors[] = 'Stock quantity cannot be negative.'; }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            'INSERT INTO products (name, category_id, supplier_id, price, stock_qty) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'siidi', $name, $category_id, $supplier_id, $price, $stock_qty);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: products.php?msg=' . urlencode("\"$name\" was added to the catalogue."));
            exit;
        } else {
            error_log('Insert product failed: ' . mysqli_stmt_error($stmt));
            $errors[] = 'An error occurred. Please try again.';
        }
    }
}

$page_title = 'Add Product';
$active = 'add_product';
include 'includes/header.php';

$categories = safe_query($conn, 'SELECT category_id, category_name FROM categories ORDER BY category_name');
$suppliers  = safe_query($conn, 'SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name');
?>

<div class="panel">
    <h2>Add New Product</h2>

    <?php foreach ($errors as $e): ?>
        <p class="alert alert-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form class="ledger-form" method="POST" action="add_product.php">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- select category --</option>
            <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $c['category_id']; ?>" <?php echo ($category_id == $c['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="supplier_id">Supplier</label>
        <select id="supplier_id" name="supplier_id" required>
            <option value="">-- select supplier --</option>
            <?php mysqli_data_seek($suppliers, 0); while ($s = mysqli_fetch_assoc($suppliers)): ?>
                <option value="<?php echo $s['supplier_id']; ?>" <?php echo ($supplier_id == $s['supplier_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['supplier_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="price">Price (TZS)</label>
        <input type="number" step="0.01" min="0.01" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>

        <label for="stock_qty">Stock Quantity</label>
        <input type="number" step="1" min="0" id="stock_qty" name="stock_qty" value="<?php echo htmlspecialchars($stock_qty); ?>" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a class="btn btn-edit" href="products.php">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
