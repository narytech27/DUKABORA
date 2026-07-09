<?php
// includes/nav.php
// $active is set by each page before including this file, e.g. $active = 'products';
$links = [
    'products'      => ['products.php', 'Products'],
    'add_product'   => ['add_product.php', 'Add Product'],
    'record_sale'   => ['record_sale.php', 'Record Sale'],
    'sales_history' => ['sales_history.php', 'Sales History'],
    'report'        => ['report.php', 'Report'],
];
?>
<nav class="ledger-nav">
    <ul>
        <?php foreach ($links as $key => [$href, $label]): ?>
            <li>
                <a href="<?php echo $href; ?>"
                   class="<?php echo (isset($active) && $active === $key) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($label); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
