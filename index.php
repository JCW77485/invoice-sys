<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$config = get_config();
$db = get_db();

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
$invoice = null;
$invoice_items = [];

if ($edit_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$edit_id]);
        $invoice = $stmt->fetch();

        if ($invoice) {
            $stmt_items = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
            $stmt_items->execute([$edit_id]);
            $invoice_items = $stmt_items->fetchAll();
        }
    } catch (Exception $e) {
        // Fallback silently if query fails
    }
}

// Generate new code/defaults if not editing or invoice not found
if (!$invoice) {
    $edit_id = 0;
    $invoice = [
        'customer_name' => 'Walk-in Customer',
        'customer_address' => '-',
        'customer_email' => 'customer@example.com',
        'invoice_number' => 'INV-' . rand(100000, 999999),
        'invoice_date' => date('Y-m-d'),
        'tax_rate' => 0.00,
        'subtotal' => 0.00,
        'tax_amount' => 0.00,
        'grand_total' => 0.00,
        'status' => 'draft'
    ];
    $invoice_items = [
        [
            'description' => 'Product/Service Description',
            'quantity' => 1.0,
            'unit_price' => 10.00,
            'total' => 10.00
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_id > 0 ? 'Edit Invoice Draft' : 'Invoice System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">Invoice System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Generator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="invoices.php">Invoices List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4"><?php echo htmlspecialchars($config['company_name']); ?></h1>
        <h4 class="text-center text-muted mb-4"><?php echo $edit_id > 0 ? 'Edit Invoice Draft' : 'Invoice Generator'; ?></h4>

        <form action="save_invoice.php" method="POST" id="invoice-form">
            <?php if ($edit_id > 0): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <?php endif; ?>
            <input type="hidden" name="status" id="invoice-status" value="<?php echo htmlspecialchars($invoice['status']); ?>">

            <div class="row mb-4">
                <div class="col-12 mb-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Company Details (Sender)</h5>
                            <p class="card-text mb-0">
                                <strong><?php echo htmlspecialchars($config['company_name']); ?></strong><br>
                                SSM: <?php echo htmlspecialchars($config['ssm']); ?><br>
                                <?php echo nl2br(htmlspecialchars($config['address'])); ?>
                            </p>
                            <small class="text-muted">* These details are loaded from the settings and will appear on the final invoice.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Customer Details</h3>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($invoice['customer_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Customer Address</label>
                        <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required><?php echo htmlspecialchars($invoice['customer_address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Customer Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($invoice['customer_email']); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Invoice Details</h3>
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="invoice_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" required>
                    </div>
                </div>
            </div>

            <h3>Items</h3>
            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width: 150px;">Quantity</th>
                            <th style="width: 150px;">Unit Price (RM)</th>
                            <th style="width: 150px;">Total (RM)</th>
                            <th style="width: 50px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <?php foreach ($invoice_items as $index => $item): ?>
                            <tr>
                                <td><input type="text" class="form-control" name="items[<?php echo $index; ?>][description]" value="<?php echo htmlspecialchars($item['description']); ?>" required></td>
                                <td><input type="number" class="form-control quantity" name="items[<?php echo $index; ?>][quantity]" step="any" value="<?php echo htmlspecialchars($item['quantity']); ?>" required></td>
                                <td><input type="number" class="form-control unit_price" name="items[<?php echo $index; ?>][unit_price]" step="0.01" value="<?php echo htmlspecialchars($item['unit_price']); ?>" required></td>
                                <td><input type="number" class="form-control row-total" name="items[<?php echo $index; ?>][total]" value="<?php echo htmlspecialchars($item['total']); ?>" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-row">Delete</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-primary mb-4" id="add-item">Add Item</button>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Subtotal (RM)</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="subtotal" name="subtotal" value="<?php echo htmlspecialchars($invoice['subtotal']); ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Tax (%)</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" value="<?php echo htmlspecialchars($invoice['tax_rate']); ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Tax Amount</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="tax_amount" name="tax_amount" value="<?php echo htmlspecialchars($invoice['tax_amount']); ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label fw-bold">Grand Total (RM)</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control fw-bold" id="grand_total" name="grand_total" value="<?php echo htmlspecialchars($invoice['grand_total']); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 d-flex justify-content-center gap-3">
                <button type="submit" id="btn-save-draft" class="btn btn-secondary btn-lg">Save as Draft</button>
                <button type="submit" id="btn-generate-invoice" class="btn btn-success btn-lg">Generate Invoice</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
