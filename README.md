# Invoice Generation System

A simple, dynamic invoice generation system built with pure HTML, PHP, CSS, Bootstrap, and JavaScript.

## Features

- **Dynamic Items:** Add or remove invoice line items on the fly.
- **Real-time Calculation:** Subtotal, tax, and grand total are calculated instantly in the browser.
- **Server-side Validation (PHP):** Totals are recalculated on the server for data integrity when using the PHP version.
- **Print-ready:** Clean and professional invoice layout optimized for printing.
- **Bootstrap UI:** Responsive design using Bootstrap 5.

## Versions

### 1. Static Version (`index.html`)
Ideal for static hosting like GitHub Pages. It uses pure JavaScript to generate the invoice view within the same page.
- **Live Demo:** [https://jcw77485.github.io/invoice-sys/index.html](https://jcw77485.github.io/invoice-sys/index.html)

### 2. PHP Version (`index.php`)
Uses PHP for server-side processing and recalculation of totals. Requires a server with PHP support.

## How to Run Locally

1. Ensure you have PHP installed on your machine.
2. Clone this repository.
3. Navigate to the project directory in your terminal.
4. Start the PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
5. Open your browser and go to `http://localhost:8000/index.php` (for PHP) or `http://localhost:8000/index.html` (for Static).

## Deployment

### Static Deployment (GitHub Pages, Vercel, etc.)
Simply upload all files. `index.html` will be the entry point.

### PHP Deployment (Apache, Nginx, etc.)
1. Upload all files to your server's web root.
2. Access `index.php` via your domain.

### Docker Deployment
1. Create a `Dockerfile`:
   ```dockerfile
   FROM php:8.2-apache
   COPY . /var/www/html/
   ```
2. Build and run:
   ```bash
   docker build -t invoice-system .
   docker run -p 8080:80 invoice-system
   ```
