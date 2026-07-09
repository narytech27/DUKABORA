<?php
/**
 * config.php
 * Shared database connection for Duka Bora Inventory System.
 * Requirement (Module 5): no raw PHP warnings/notices should ever reach the
 * screen — errors are logged instead of displayed, and every DB call below
 * is wrapped so a friendly message is shown if something goes wrong.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);   // never show raw PHP errors to the user
ini_set('log_errors', 1);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'duka_bora';

// Turn off mysqli's default "throw warnings" behaviour so we can control
// error handling ourselves with a friendly message.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('<p style="color:red; font-family:sans-serif; padding:2rem;">
         An error occurred connecting to the database. Please try again later.</p>');
}

mysqli_set_charset($conn, 'utf8mb4');

/**
 * Wraps a mysqli_query call and shows a friendly message on failure
 * instead of leaking raw SQL errors (Module 5 requirement).
 */
function safe_query(mysqli $conn, string $sql, mysqli_stmt $stmt = null) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        error_log('SQL error: ' . mysqli_error($conn) . ' | Query: ' . $sql);
        echo '<p class="alert alert-error">An error occurred. Please try again.</p>';
        return false;
    }
    return $result;
}
