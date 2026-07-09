<?php
// includes/header.php
// Expects $page_title and optionally $active (for nav highlighting) to be
// set by the including page before this file is required.
if (!isset($page_title)) { $page_title = 'Duka Bora'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?> — Duka Bora</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="shop-header">
    <div class="inner">
        <h1 class="shop-title">Duka Bora
            <small>Online Market Inventory System</small>
        </h1>
        <span class="page-label"><?php echo htmlspecialchars($page_title); ?></span>
    </div>
</header>

<?php include 'includes/nav.php'; ?>

<div class="wrap">
