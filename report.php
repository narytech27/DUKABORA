<?php
require 'config.php';

$page_title = 'Report';
$active = 'report';
include 'includes/header.php';

// (a) total sales recorded today
$today_result = safe_query($conn,
    "SELECT COUNT(*) AS sale_count, COALESCE(SUM(total_price), 0) AS total_today
     FROM sales
     WHERE DATE(sale_date) = CURDATE()");
$today = mysqli_fetch_assoc($today_result);

// (b) top 3 bestselling products by total qty_sold
$top_result = safe_query($conn,
    "SELECT p.name, SUM(s.qty_sold) AS total_qty
     FROM sales s
     JOIN products p ON s.product_id = p.product_id
     GROUP BY s.product_id, p.name
     ORDER BY total_qty DESC
     LIMIT 3");

// (c) low stock alert: qty < 5
$low_stock_result = safe_query($conn,
    "SELECT name, stock_qty FROM products WHERE stock_qty < 5 ORDER BY stock_qty ASC");
?>

<div class="panel-row">
    <div class="stat-tile">
        <div class="value"><?php echo (int)$today['sale_count']; ?></div>
        <div class="label">Sales Recorded Today</div>
    </div>
    <div class="stat-tile">
        <div class="value">TZS <?php echo number_format($today['total_today'], 2); ?></div>
        <div class="label">Revenue Today</div>
    </div>
</div>

<div class="panel">
    <h2>Top 3 Bestselling Products</h2>
    <table class="ledger">
        <thead>
            <tr><th>Rank</th><th>Product</th><th>Total Units Sold</th></tr>
        </thead>
        <tbody>
        <?php if ($top_result && mysqli_num_rows($top_result) > 0): $rank = 1; ?>
            <?php while ($t = mysqli_fetch_assoc($top_result)): ?>
                <tr>
                    <td data-label="Rank"><?php echo $rank++; ?></td>
                    <td data-label="Product"><?php echo htmlspecialchars($t['name']); ?></td>
                    <td data-label="Units Sold" class="num"><?php echo $t['total_qty']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No sales recorded yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="panel">
    <h2>Low Stock Alert <span class="stamp low">qty &lt; 5</span></h2>
    <table class="ledger">
        <thead>
            <tr><th>Product</th><th>Remaining Stock</th></tr>
        </thead>
        <tbody>
        <?php if ($low_stock_result && mysqli_num_rows($low_stock_result) > 0): ?>
            <?php while ($l = mysqli_fetch_assoc($low_stock_result)): ?>
                <tr>
                    <td data-label="Product"><?php echo htmlspecialchars($l['name']); ?></td>
                    <td data-label="Stock" class="num">
                        <span class="stamp <?php echo $l['stock_qty'] == 0 ? 'out' : 'low'; ?>">
                            <?php echo (int)$l['stock_qty']; ?> left
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">All products are sufficiently stocked.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
