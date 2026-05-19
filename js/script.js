document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('items-body');
    const addItemBtn = document.getElementById('add-item');
    const subtotalInput = document.getElementById('subtotal');
    const taxRateInput = document.getElementById('tax_rate');
    const taxAmountInput = document.getElementById('tax_amount');
    const grandTotalInput = document.getElementById('grand_total');
    let rowCount = 1;

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

    taxRateInput.addEventListener('input', calculateTotals);

    // Initial calculation
    calculateTotals();
});
