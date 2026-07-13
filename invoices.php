<?php
require_once __DIR__ . '/db.php';

$db = get_db();
$message = '';
$status_class = '';

// Handle invoice deletion if requested
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    try {
        // Cascade delete should remove associated items if setup correctly, but let's do it manually as SQLite sometimes needs foreign keys turned on.
        $db->exec("PRAGMA foreign_keys = ON;");
        $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "Invoice deleted successfully.";
        $status_class = "alert-success";
    } catch (Exception $e) {
        $message = "Error deleting invoice: " . $e->getMessage();
        $status_class = "alert-danger";
    }
}

// Check for redirect messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'draft_saved') {
        $message = "Draft saved successfully!";
        $status_class = "alert-success";
    }
}

// Fetch invoices
$invoices = [];
try {
    $stmt = $db->query("SELECT * FROM invoices ORDER BY created_at DESC");
    $invoices = $stmt->fetchAll();
} catch (Exception $e) {
    $message = "Error fetching invoices: " . $e->getMessage();
    $status_class = "alert-danger";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices List - Invoice System</title>
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
                        <a class="nav-link active" href="invoices.php">Invoices List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4">Invoices List</h1>
        <h4 class="text-center text-muted mb-4">View and manage generated invoices and drafts</h4>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $status_class; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-secondary mb-0">Total Invoices: <?php echo count($invoices); ?></h5>
            <a href="index.php" class="btn btn-primary">Create New Invoice</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Date</th>
                        <th>Customer Name</th>
                        <th>Grand Total (RM)</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No invoices found. Generate some invoices or save drafts!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($inv['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($inv['invoice_date']); ?></td>
                                <td><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                                <td class="fw-bold"><?php echo number_format($inv['grand_total'], 2); ?></td>
                                <td class="text-center">
                                    <?php if ($inv['status'] === 'draft'): ?>
                                        <span class="badge bg-secondary px-2 py-1">Draft</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-2 py-1">Final</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($inv['status'] === 'draft'): ?>
                                        <a href="index.php?edit_id=<?php echo $inv['id']; ?>" class="btn btn-warning btn-sm">Edit/Resume</a>
                                    <?php else: ?>
                                        <a href="invoice.php?id=<?php echo $inv['id']; ?>" class="btn btn-info btn-sm text-white">View/Print</a>
                                    <?php endif; ?>
                                    <a href="invoices.php?action=delete&id=<?php echo $inv['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
