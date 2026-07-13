<?php
function get_db() {
    $db_file = __DIR__ . '/invoice.db';
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize database schema
    $db->exec("CREATE TABLE IF NOT EXISTS invoices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        invoice_number TEXT NOT NULL UNIQUE,
        invoice_date TEXT NOT NULL,
        customer_name TEXT NOT NULL,
        customer_address TEXT,
        customer_email TEXT,
        tax_rate REAL DEFAULT 0.0,
        subtotal REAL DEFAULT 0.0,
        tax_amount REAL DEFAULT 0.0,
        grand_total REAL DEFAULT 0.0,
        status TEXT DEFAULT 'draft', -- 'draft' or 'final'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS invoice_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        invoice_id INTEGER NOT NULL,
        description TEXT NOT NULL,
        quantity REAL DEFAULT 0.0,
        unit_price REAL DEFAULT 0.0,
        total REAL DEFAULT 0.0,
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
    )");

    return $db;
}
