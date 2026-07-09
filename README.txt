DUKA BORA — ONLINE MARKET INVENTORY SYSTEM
IS 181 Group Assignment 2 (PHP + MySQL)
============================================================

SETUP INSTRUCTIONS (XAMPP / localhost)
----------------------------------------------------------
1. Install XAMPP (https://www.apachefriends.org/) if not already installed.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Copy this whole "duka_bora" folder into your XAMPP htdocs directory,
   e.g. C:\xampp\htdocs\duka_bora  (Windows) or /Applications/XAMPP/htdocs/duka_bora (Mac).
4. Open phpMyAdmin (http://localhost/phpmyadmin).
5. Click "Import" and select database.sql, OR open the SQL tab and paste
   its contents, then run it. This creates the duka_bora database with
   all four tables and seed data (11 products, 3 categories, 3 suppliers).
6. If your MySQL root user has a password, or you use a different DB user,
   update the credentials at the top of config.php:
       $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME
7. Visit http://localhost/duka_bora/products.php in your browser.

FOLDER STRUCTURE
----------------------------------------------------------
duka_bora/
  database.sql          Module 1 — schema + seed data
  config.php             Shared MySQL connection (mysqli)
  products.php           Module 2 — product list, stock badges, cookie note
  add_product.php        Module 2 — add product form + insert
  edit_product.php       Module 2 — edit product form + update
  delete_product.php     Module 2 — delete product
  record_sale.php        Module 3 — record a sale, deducts stock
  sales_history.php      Module 3 — JOIN query, all past sales
  report.php              Module 3 — today's totals, top 3, low stock alert
  includes/nav.php       Module 4 — shared navigation bar
  includes/header.php    Module 4 — shared page header
  includes/footer.php    Module 4 — shared page footer
  css/style.css           Module 4 — styling, responsive layout, badges

WHAT STILL NEEDS TO BE DONE BY YOUR GROUP
----------------------------------------------------------
This code covers Modules 1-5 (95 of the 100 marks). The remaining 5 marks
are the Group Report (report.pdf), which must be written by your group and
cannot be generated for you:
  - 2-3 pages: introduction, group roles, screenshots of each working page,
    challenges and conclusions.
  - Contribution Table listing each student's specific tasks (Section 8 of
    the brief) — every member must show individually identifiable work.
  - The signed AI Use Declaration (Section 9) — this assignment brief
    requires your group to disclose that AI was used, which module(s) it
    was used for, and how each student reviewed / understood / modified
    the output before submitting. Skipping this declaration where AI was
    used results in a mark of zero for the affected module(s).

Before your live demo, make sure every member can explain and defend the
code in their assigned module — that is the whole point of the exercise.
