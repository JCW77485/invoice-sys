document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('items-body');
    const addItemBtn = document.getElementById('add-item');
    const subtotalInput = document.getElementById('subtotal');
    const taxRateInput = document.getElementById('tax_rate');
    const taxAmountInput = document.getElementById('tax_amount');
    const grandTotalInput = document.getElementById('grand_total');
    const invoiceForm = document.getElementById('invoice-form');

    // Elements for static display (index.html)
    const generatorView = document.getElementById('generator-view');
    const invoiceView = document.getElementById('invoice-view');
    const backToGeneratorBtn = document.getElementById('back-to-generator');

    let rowCount = 1;

    // Set today's date as default
    const dateInput = document.getElementById('invoice_date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }

    // Set default invoice number for index.html if empty
    const invoiceNumInput = document.getElementById('invoice_number');
    if (invoiceNumInput && !invoiceNumInput.value) {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        invoiceNumInput.value = 'INV-' + randomNum;
    }

    function calculateTotals() {
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
                <td><input type="number" class="form-control quantity" name="items[${rowCount}][quantity]" min="1" step="any" value="1" required></td>
                <td><input type="number" class="form-control unit_price" name="items[${rowCount}][unit_price]" min="0" step="0.01" value="0.00" required></td>
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

    // Handle form submission for static version
    if (invoiceForm && invoiceView) {
        invoiceForm.addEventListener('submit', function(e) {
            // Check if we are on index.html or index.php
            // index.php form has action="invoice.php"
            if (!invoiceForm.hasAttribute('action')) {
                e.preventDefault();

                // Populate invoice view
                document.getElementById('display_customer_name').textContent = document.getElementById('customer_name').value;
                document.getElementById('display_customer_address').textContent = document.getElementById('customer_address').value;
                document.getElementById('display_customer_email').textContent = document.getElementById('customer_email').value;
                document.getElementById('display_invoice_number').textContent = document.getElementById('invoice_number').value;
                document.getElementById('display_invoice_date').textContent = document.getElementById('invoice_date').value;

                const itemsDisplay = document.getElementById('display_items');
                itemsDisplay.innerHTML = '';

                const rows = itemsBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const desc = row.querySelector('input[name*="[description]"]').value;
                    const qty = row.querySelector('.quantity').value;
                    const price = row.querySelector('.unit_price').value;
                    const total = row.querySelector('.row-total').value;

                    const tr = document.createElement('tr');

                    const descCell = document.createElement('td');
                    descCell.textContent = desc;

                    const qtyCell = document.createElement('td');
                    qtyCell.textContent = qty;

                    const priceCell = document.createElement('td');
                    priceCell.textContent = '$' + parseFloat(price).toFixed(2);

                    const totalCell = document.createElement('td');
                    totalCell.textContent = '$' + parseFloat(total).toFixed(2);

                    tr.appendChild(descCell);
                    tr.appendChild(qtyCell);
                    tr.appendChild(priceCell);
                    tr.appendChild(totalCell);

                    itemsDisplay.appendChild(tr);
                });

                document.getElementById('display_subtotal').textContent = subtotalInput.value;
                document.getElementById('display_tax_rate').textContent = taxRateInput.value;
                document.getElementById('display_tax_amount').textContent = taxAmountInput.value;
                document.getElementById('display_grand_total').textContent = grandTotalInput.value;

                // Switch views
                generatorView.style.display = 'none';
                invoiceView.style.display = 'block';
                window.scrollTo(0, 0);
            }
        });
    }

    if (backToGeneratorBtn) {
        backToGeneratorBtn.addEventListener('click', function() {
            invoiceView.style.display = 'none';
            generatorView.style.display = 'block';
            window.scrollTo(0, 0);
        });
    }

    // Initial calculation
    if (itemsBody) {
        calculateTotals();
    }
});
