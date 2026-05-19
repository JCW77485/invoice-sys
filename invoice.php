<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$customer_name = $_POST['customer_name'] ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$customer_email = $_POST['customer_email'] ?? '';
$invoice_number = $_POST['invoice_number'] ?? '';
$invoice_date = $_POST['invoice_date'] ?? '';
$items = $_POST['items'] ?? [];
$tax_rate = (float)($_POST['tax_rate'] ?? 0);

// Recalculate on server side for security
$subtotal = 0;
foreach ($items as $key => $item) {
    $qty = (float)($item['quantity'] ?? 0);
    $price = (float)($item['unit_price'] ?? 0);
    $item_total = $qty * $price;
    $items[$key]['total'] = $item_total;
    $subtotal += $item_total;
}

$tax_amount = $subtotal * ($tax_rate / 100);
$grand_total = $subtotal + $tax_amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($invoice_number); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
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
            <a href="index.php" class="btn btn-secondary">Back to Generator</a>
        </div>

        <div class="invoice-box">
            <div class="row mb-4">
                <div class="col-6">
                    <h2>INVOICE</h2>
                </div>
                <div class="col-6 text-end">
                    <p>
                        Invoice #: <?php echo htmlspecialchars($invoice_number); ?><br>
                        Created: <?php echo htmlspecialchars($invoice_date); ?>
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <h5>From:</h5>
                    <p>
                        <strong>NETVORA STUDIO</strong><br>
                        202603133337<br>
                        416, Jalan Ida 1B, 47000 Sungai Buloh, Selangor
                    </p>
                </div>
                <div class="col-6 text-end">
                    <h5>To:</h5>
                    <p>
                        <?php echo htmlspecialchars($customer_name); ?><br>
                        <?php echo nl2br(htmlspecialchars($customer_address)); ?><br>
                        <?php echo htmlspecialchars($customer_email); ?>
                    </p>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>RM<?php echo number_format((float)$item['unit_price'], 2); ?></td>
                            <td>RM<?php echo number_format((float)$item['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-end">RM<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tax (<?php echo htmlspecialchars((string)$tax_rate); ?>%):</strong></td>
                            <td class="text-end">RM<?php echo number_format($tax_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-end"><strong>RM<?php echo number_format($grand_total, 2); ?></strong></td>
                        </tr>
                    </table>
                </div>
                *Make sure payment within 10 days*
                Bank Acc: 564584401054 (Maybank)
            </div>
        </div>
    </div>
</body>
</html>
