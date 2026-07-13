<?php
require_once __DIR__ . '/config.php';

$message = '';
$status_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_data = [
        'company_name' => $_POST['company_name'] ?? '',
        'ssm' => $_POST['ssm'] ?? '',
        'address' => $_POST['address'] ?? '',
        'payment_instructions' => $_POST['payment_instructions'] ?? '',
        'bank_name' => $_POST['bank_name'] ?? '',
        'bank_acc' => $_POST['bank_acc'] ?? '',
        'account_holder' => $_POST['account_holder'] ?? ''
    ];

    if (save_config($updated_data)) {
        $message = 'Configuration saved successfully!';
        $status_class = 'alert-success';
    } else {
        $message = 'Failed to save configuration.';
        $status_class = 'alert-danger';
    }
}

$config = get_config();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sender Settings - Invoice System</title>
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
                        <a class="nav-link" href="index.php">Generator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="invoices.php">Invoices List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4">Sender Information Settings</h1>
        <h4 class="text-center text-muted mb-4">Configure Default Invoice Sender details</h4>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $status_class; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="settings.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="company_name" class="form-label fw-bold">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($config['company_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ssm" class="form-label fw-bold">SSM/Registration Number</label>
                    <input type="text" class="form-control" id="ssm" name="ssm" value="<?php echo htmlspecialchars($config['ssm']); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label fw-bold">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($config['address']); ?></textarea>
            </div>

            <hr class="my-4">
            <h3>Payment Details</h3>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="bank_name" class="form-label fw-bold">Bank Name</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($config['bank_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="bank_acc" class="form-label fw-bold">Bank Account Number</label>
                    <input type="text" class="form-control" id="bank_acc" name="bank_acc" value="<?php echo htmlspecialchars($config['bank_acc']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="account_holder" class="form-label fw-bold">Account Holder Name</label>
                    <input type="text" class="form-control" id="account_holder" name="account_holder" value="<?php echo htmlspecialchars($config['account_holder']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="payment_instructions" class="form-label fw-bold">Payment Instructions</label>
                    <input type="text" class="form-control" id="payment_instructions" name="payment_instructions" value="<?php echo htmlspecialchars($config['payment_instructions']); ?>" required>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                <a href="index.php" class="btn btn-secondary btn-lg">Back to Generator</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
