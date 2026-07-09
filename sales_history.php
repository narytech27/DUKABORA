<?php
require 'config.php';

$page_title = 'Sales History';
$active = 'sales_history';
include 'includes/header.php';
?>

<div class="panel">
    <h2>Sales History</h2>

    <?php
    $sql = "SELECT s.sale_id, p.name, s.qty_sold, s.total_price, s.sale_date
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            ORDER BY s.sale_date DESC";
    $result = safe_query($conn, $sql);
    ?>

    <table class="ledger">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty Sold</th>
                <th>Total (TZS)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($s = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td data-label="#"><?php echo $s['sale_id']; ?></td>
                    <td data-label="Product"><?php echo htmlspecialchars($s['name']); ?></td>
                    <td data-label="Qty Sold" class="num"><?php echo $s['qty_sold']; ?></td>
                    <td data-label="Total" class="num"><?php echo number_format($s['total_price'], 2); ?></td>
                    <td data-label="Date"><?php echo date('d M Y, H:i', strtotime($s['sale_date'])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No sales recorded yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
