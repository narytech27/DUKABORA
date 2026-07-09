<?php
require 'config.php';

// ---------- Module 5: cookie — remember last product viewed ----------
// Clicking a product name links back here with ?view=ID, we store the
// name in a cookie, then redirect to the clean URL so refreshing the
// page doesn't re-fire the cookie write.
if (isset($_GET['view'])) {
    $view_id = intval($_GET['view']);
    $stmt = mysqli_prepare($conn, 'SELECT name FROM products WHERE product_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $view_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        setcookie('last_viewed_product', $row['name'], time() + 86400, '/');
    }
    mysqli_stmt_close($stmt);
    header('Location: products.php');
    exit;
}

$page_title = 'Products';
$active = 'products';
include 'includes/header.php';

// ---------- success/error banner from add/edit/delete redirects ----------
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';
?>

<?php if ($msg): ?>
    <p class="alert alert-success"><?php echo htmlspecialchars($msg); ?></p>
<?php endif; ?>
<?php if ($err): ?>
    <p class="alert alert-error"><?php echo htmlspecialchars($err); ?></p>
<?php endif; ?>

<?php if (isset($_COOKIE['last_viewed_product'])): ?>
    <p class="ledger-note">Last viewed product: <?php echo htmlspecialchars($_COOKIE['last_viewed_product']); ?></p>
<?php endif; ?>

<div class="panel">
    <h2>Product Catalogue</h2>

    <?php
    $sql = "SELECT p.product_id, p.name, p.price, p.stock_qty,
                   c.category_name, s.supplier_name
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            JOIN suppliers  s ON p.supplier_id = s.supplier_id
            ORDER BY p.name ASC";
    $result = safe_query($conn, $sql);
    ?>

    <table class="ledger">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Price (TZS)</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($result)): ?>
                <?php
                    $qty = (int)$p['stock_qty'];
                    if ($qty === 0)      { $stamp = 'out'; $label = 'Out of Stock'; }
                    elseif ($qty < 10)   { $stamp = 'low'; $label = 'Low Stock'; }
                    else                 { $stamp = 'in';  $label = 'In Stock'; }
                ?>
                <tr>
                    <td data-label="Product">
                        <a href="products.php?view=<?php echo (int)$p['product_id']; ?>">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </a>
                    </td>
                    <td data-label="Category"><?php echo htmlspecialchars($p['category_name']); ?></td>
                    <td data-label="Supplier"><?php echo htmlspecialchars($p['supplier_name']); ?></td>
                    <td data-label="Price" class="num"><?php echo number_format($p['price'], 2); ?></td>
                    <td data-label="Stock" class="num"><?php echo $qty; ?></td>
                    <td data-label="Status"><span class="stamp <?php echo $stamp; ?>"><?php echo $label; ?></span></td>
                    <td data-label="Actions">
                        <div class="actions">
                            <a class="btn btn-edit" href="edit_product.php?id=<?php echo (int)$p['product_id']; ?>">Edit</a>
                            <a class="btn btn-delete" href="delete_product.php?id=<?php echo (int)$p['product_id']; ?>"
                               onclick="return confirm('Delete this product? This cannot be undone.');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No products found. <a href="add_product.php">Add one</a>.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
