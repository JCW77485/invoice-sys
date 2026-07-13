<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
$status = $_POST['status'] ?? 'draft'; // 'draft' or 'final'

$customer_name = $_POST['customer_name'] ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$customer_email = $_POST['customer_email'] ?? '';
$invoice_number = $_POST['invoice_number'] ?? '';
$invoice_date = $_POST['invoice_date'] ?? '';
$items = $_POST['items'] ?? [];
$tax_rate = (float)($_POST['tax_rate'] ?? 0);

// Server-side recalculation of totals
$subtotal = 0;
$calculated_items = [];
foreach ($items as $item) {
    $desc = $item['description'] ?? '';
    if (empty($desc)) continue; // skip blank item rows

    $qty = (float)($item['quantity'] ?? 0);
    $price = (float)($item['unit_price'] ?? 0);
    $item_total = $qty * $price;

    $calculated_items[] = [
        'description' => $desc,
        'quantity' => $qty,
        'unit_price' => $price,
        'total' => $item_total
    ];
    $subtotal += $item_total;
}

$tax_amount = $subtotal * ($tax_rate / 100);
$grand_total = $subtotal + $tax_amount;

try {
    $db = get_db();
    $db->beginTransaction();

    if ($edit_id > 0) {
        // Update existing invoice
        // Ensure the invoice number is unique (except for current record)
        $stmt = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ? AND id != ?");
        $stmt->execute([$invoice_number, $edit_id]);
        if ($stmt->fetch()) {
            // Generate a unique fallback invoice number if there's conflict
            $invoice_number .= '-' . rand(10, 99);
        }

        $stmt_update = $db->prepare("UPDATE invoices SET
            invoice_number = ?,
            invoice_date = ?,
            customer_name = ?,
            customer_address = ?,
            customer_email = ?,
            tax_rate = ?,
            subtotal = ?,
            tax_amount = ?,
            grand_total = ?,
            status = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ?");
        $stmt_update->execute([
            $invoice_number,
            $invoice_date,
            $customer_name,
            $customer_address,
            $customer_email,
            $tax_rate,
            $subtotal,
            $tax_amount,
            $grand_total,
            $status,
            $edit_id
        ]);

        $invoice_id = $edit_id;

        // Clear existing items and re-insert them
        $stmt_delete = $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $stmt_delete->execute([$invoice_id]);
    } else {
        // Check for duplicate invoice number and append random suffix if needed
        $stmt = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
        $stmt->execute([$invoice_number]);
        if ($stmt->fetch()) {
            $invoice_number .= '-' . rand(10, 99);
        }

        // Insert new invoice
        $stmt_insert = $db->prepare("INSERT INTO invoices (
            invoice_number, invoice_date, customer_name, customer_address, customer_email,
            tax_rate, subtotal, tax_amount, grand_total, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->execute([
            $invoice_number,
            $invoice_date,
            $customer_name,
            $customer_address,
            $customer_email,
            $tax_rate,
            $subtotal,
            $tax_amount,
            $grand_total,
            $status
        ]);
        $invoice_id = $db->lastInsertId();
    }

    // Insert items
    $stmt_item_insert = $db->prepare("INSERT INTO invoice_items (
        invoice_id, description, quantity, unit_price, total
    ) VALUES (?, ?, ?, ?, ?)");

    foreach ($calculated_items as $item) {
        $stmt_item_insert->execute([
            $invoice_id,
            $item['description'],
            $item['quantity'],
            $item['unit_price'],
            $item['total']
        ]);
    }

    $db->commit();

    if ($status === 'draft') {
        // Redirect to invoices list
        header('Location: invoices.php?msg=draft_saved');
    } else {
        // Redirect to view invoice print-ready page
        header('Location: invoice.php?id=' . $invoice_id);
    }
    exit;

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    die("Database Error: " . $e->getMessage());
}
