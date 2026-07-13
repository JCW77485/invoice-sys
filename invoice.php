<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($invoice_id <= 0) {
    header('Location: invoices.php');
    exit;
}

$db = get_db();
$config = get_config();

try {
    // Fetch invoice details
    $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        die("Invoice not found.");
    }

    // Fetch items
    $stmt_items = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
    $stmt_items->execute([$invoice_id]);
    $items = $stmt_items->fetchAll();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$subtotal = (float)$invoice['subtotal'];
$tax_rate = (float)$invoice['tax_rate'];
$tax_amount = (float)$invoice['tax_amount'];
$grand_total = (float)$invoice['grand_total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 40px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="text-center mb-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
            <a href="invoices.php" class="btn btn-secondary">Invoices List</a>
            <a href="index.php" class="btn btn-success">Create New</a>
        </div>

        <div class="invoice-box">
            <div class="row mb-4">
                <div class="col-6">
                    <h2>INVOICE</h2>
                </div>
                <div class="col-6 text-end">
                    <p>
                        Invoice #: <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
                        Created: <?php echo htmlspecialchars($invoice['invoice_date']); ?>
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <h5>From:</h5>
                    <p>
                        <strong><?php echo htmlspecialchars($config['company_name']); ?></strong><br>
                        SSM: <?php echo htmlspecialchars($config['ssm']); ?><br>
                        <?php echo nl2br(htmlspecialchars($config['address'])); ?>
                    </p>
                </div>
                <div class="col-6 text-end">
                    <h5>To:</h5>
                    <p>
                        <?php echo htmlspecialchars($invoice['customer_name']); ?><br>
                        <?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?><br>
                        <?php echo htmlspecialchars($invoice['customer_email']); ?>
                    </p>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price (RM)</th>
                        <th class="text-end">Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td class="text-end"><?php echo number_format((float)$item['unit_price'], 2); ?></td>
                            <td class="text-end"><?php echo number_format((float)$item['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row justify-content-end mb-5">
                <div class="col-5">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">RM <?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Tax (<?php echo htmlspecialchars((string)$tax_rate); ?>%)</td>
                            <td class="text-end">RM <?php echo number_format($tax_amount, 2); ?></td>
                        </tr>
                        <tr class="border-top border-dark">
                            <td><strong>Total Amount</strong></td>
                            <td class="text-end"><strong>RM <?php echo number_format($grand_total, 2); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="payment-info mt-5 pt-4 border-top">
                <p class="mb-1"><strong>Payment Instructions:</strong></p>
                <p class="mb-0 text-dark"><?php echo htmlspecialchars($config['payment_instructions']); ?></p>
                <p class="mb-0 text-dark">Bank Acc: <strong><?php echo htmlspecialchars($config['bank_acc']); ?></strong> (<?php echo htmlspecialchars($config['bank_name']); ?>)</p>
                <p class="mb-0 text-dark">Account Holder: <strong><?php echo htmlspecialchars($config['account_holder']); ?></strong></p>
            </div>

            <div class="mt-5 text-center text-muted">
                <small>Thank you for your business!</small>
            </div>
        </div>
    </div>
</body>
</html>
