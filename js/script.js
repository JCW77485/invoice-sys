document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('items-body');
    const addItemBtn = document.getElementById('add-item');
    const subtotalInput = document.getElementById('subtotal');
    const taxRateInput = document.getElementById('tax_rate');
    const taxAmountInput = document.getElementById('tax_amount');
    const grandTotalInput = document.getElementById('grand_total');
    const invoiceForm = document.getElementById('invoice-form');

    // Get number of existing rows to set initial rowCount correctly
    let rowCount = itemsBody ? itemsBody.querySelectorAll('tr').length : 1;

    // Set today's date as default if empty
    const dateInput = document.getElementById('invoice_date');
    if (dateInput && !dateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }

    // Set default invoice number if empty
    const invoiceNumInput = document.getElementById('invoice_number');
    if (invoiceNumInput && !invoiceNumInput.value) {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        invoiceNumInput.value = 'INV-' + randomNum;
    }

    function calculateTotals() {
        if (!itemsBody) return;
        let subtotal = 0;
        const rows = itemsBody.querySelectorAll('tr');

        rows.forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit_price').value) || 0;
            const total = quantity * unitPrice;
            row.querySelector('.row-total').value = total.toFixed(2);
            subtotal += total;
        });

        const taxRate = parseFloat(taxRateInput.value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const grandTotal = subtotal + taxAmount;

        subtotalInput.value = subtotal.toFixed(2);
        taxAmountInput.value = taxAmount.toFixed(2);
        grandTotalInput.value = grandTotal.toFixed(2);
    }

    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" class="form-control" name="items[${rowCount}][description]" required></td>
                <td><input type="number" class="form-control quantity" name="items[${rowCount}][quantity]" step="any" value="1" required></td>
                <td><input type="number" class="form-control unit_price" name="items[${rowCount}][unit_price]" step="0.01" value="0.00" required></td>
                <td><input type="number" class="form-control row-total" name="items[${rowCount}][total]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Delete</button></td>
            `;
            itemsBody.appendChild(newRow);
            rowCount++;
            calculateTotals();
        });
    }

    if (itemsBody) {
        itemsBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        itemsBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit_price')) {
                calculateTotals();
            }
        });
    }

    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateTotals);
    }

    // Handle draft status setting on click
    const btnSaveDraft = document.getElementById('btn-save-draft');
    const btnGenerateInvoice = document.getElementById('btn-generate-invoice');
    const statusInput = document.getElementById('invoice-status');

    if (btnSaveDraft && statusInput) {
        btnSaveDraft.addEventListener('click', function() {
            statusInput.value = 'draft';
        });
    }

    if (btnGenerateInvoice && statusInput) {
        btnGenerateInvoice.addEventListener('click', function() {
            statusInput.value = 'final';
        });
    }

    // Initial calculation
    if (itemsBody) {
        calculateTotals();
    }
});
