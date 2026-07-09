<?php
require 'config.php';

$errors = [];
$selected_product_id = '';
$qty_sold_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_product_id = intval($_POST['product_id'] ?? 0);
    $qty_sold_input       = $_POST['qty_sold'] ?? '';
    $qty_sold             = intval($qty_sold_input);

    if ($selected_product_id <= 0) { $errors[] = 'Please select a product.'; }
    if ($qty_sold <= 0)            { $errors[] = 'Quantity sold must be a positive whole number.'; }

    $product = null;
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, 'SELECT product_id, name, price, stock_qty FROM products WHERE product_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $selected_product_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$product) {
            $errors[] = 'Selected product could not be found.';
        } elseif ($qty_sold > (int)$product['stock_qty']) {
            // ---------- Module 3 & 5: never allow selling more than available ----------
            $errors[] = "Only {$product['stock_qty']} unit(s) of \"{$product['name']}\" are in stock.";
        }
    }

    if (empty($errors)) {
        $total_price = $qty_sold * (float)$product['price'];

        mysqli_begin_transaction($conn);
        try {
            $stmt1 = mysqli_prepare($conn,
                'INSERT INTO sales (product_id, qty_sold, total_price) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt1, 'iid', $selected_product_id, $qty_sold, $total_price);
            mysqli_stmt_execute($stmt1);

            $stmt2 = mysqli_prepare($conn,
                'UPDATE products SET stock_qty = stock_qty - ? WHERE product_id = ? AND stock_qty >= ?');
            mysqli_stmt_bind_param($stmt2, 'iii', $qty_sold, $selected_product_id, $qty_sold);
            mysqli_stmt_execute($stmt2);

            if (mysqli_stmt_affected_rows($stmt2) === 0) {
                // stock changed between our check and the update (race condition) — abort
                throw new Exception('stock changed');
            }

            mysqli_commit($conn);
            mysqli_stmt_close($stmt1);
            mysqli_stmt_close($stmt2);

            $msg = "Recorded sale: {$qty_sold} x \"{$product['name']}\" (TZS " . number_format($total_price, 2) . ")";
            header('Location: record_sale.php?msg=' . urlencode($msg));
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            error_log('Sale transaction failed: ' . $e->getMessage());
            $errors[] = 'An error occurred while recording the sale. Please try again.';
        }
    }
}

$page_title = 'Record Sale';
$active = 'record_sale';
include 'includes/header.php';

$products = safe_query($conn, 'SELECT product_id, name, price, stock_qty FROM products ORDER BY name');
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg): ?>
    <p class="alert alert-success"><?php echo htmlspecialchars($msg); ?></p>
<?php endif; ?>

<div class="panel">
    <h2>Record a Sale</h2>

    <?php foreach ($errors as $e): ?>
        <p class="alert alert-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form class="ledger-form" method="POST" action="record_sale.php">
        <label for="product_id">Product</label>
        <select id="product_id" name="product_id" required>
            <option value="">-- select product --</option>
            <?php mysqli_data_seek($products, 0); while ($p = mysqli_fetch_assoc($products)): ?>
                <option value="<?php echo $p['product_id']; ?>"
                    data-stock="<?php echo $p['stock_qty']; ?>"
                    <?php echo ($selected_product_id == $p['product_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p['name']); ?>
                    (<?php echo $p['stock_qty']; ?> in stock, TZS <?php echo number_format($p['price'], 2); ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="qty_sold">Quantity Sold</label>
        <input type="number" step="1" min="1" id="qty_sold" name="qty_sold" value="<?php echo htmlspecialchars($qty_sold_input); ?>" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Record Sale</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
