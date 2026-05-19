<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">NETVORA STUDIO</h1>
        <h4 class="text-center text-muted mb-4">Invoice Generator</h4>
        <form action="invoice.php" method="POST" id="invoice-form">
            <div class="row mb-4">
                <div class="col-12 mb-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Company Details (Sender)</h5>
                            <p class="card-text mb-0">
                                <strong>NETVORA STUDIO</strong><br>
                                 202603133337<br>
                                16, Jalan Ida 1B, 47000 Sungai Buloh, Selangor
                            </p>
                            <small class="text-muted">* These details are fixed and will appear on the final invoice.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Customer Details</h3>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="Walk-in Customer" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Customer Address</label>
                        <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required>-</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Customer Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" value="customer@example.com" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Invoice Details</h3>
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="INV-<?php echo rand(100000, 999999); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="invoice_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
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
                            <th style="width: 150px;">Unit Price</th>
                            <th style="width: 150px;">Total</th>
                            <th style="width: 50px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr>
                            <td><input type="text" class="form-control" name="items[0][description]" value="Product/Service Description" required></td>
                            <td><input type="number" class="form-control quantity" name="items[0][quantity]" min="1" step="any" value="1" required></td>
                            <td><input type="number" class="form-control unit_price" name="items[0][unit_price]" min="0" step="0.01" value="10.00" required></td>
                            <td><input type="number" class="form-control row-total" name="items[0][total]" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Delete</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-primary mb-4" id="add-item">Add Item</button>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Subtotal</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="subtotal" name="subtotal" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Tax (%)</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" min="0" step="0.01" value="0.00">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label">Tax Amount</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="tax_amount" name="tax_amount" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-6 col-form-label fw-bold">Grand Total</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control fw-bold" id="grand_total" name="grand_total" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg">Generate Invoice</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
