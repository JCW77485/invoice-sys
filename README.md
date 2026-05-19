# Invoice Generation System

A simple, dynamic invoice generation system built with pure HTML, PHP, CSS, Bootstrap, and JavaScript.

## Features

- **Dynamic Items:** Add or remove invoice line items on the fly.
- **Real-time Calculation:** Subtotal, tax, and grand total are calculated instantly in the browser.
- **Server-side Validation:** Totals are recalculated on the server for data integrity.
- **Print-ready:** Clean and professional invoice layout optimized for printing.
- **Bootstrap UI:** Responsive design using Bootstrap 5.

## How to Run Locally

1. Ensure you have PHP installed on your machine.
2. Clone this repository.
3. Navigate to the project directory in your terminal.
4. Start the PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
5. Open your browser and go to `http://localhost:8000`.

## Deployment

Since this is a pure PHP application, it can be deployed to any web server that supports PHP (like Apache or Nginx).

### Basic Deployment Steps:

1. Upload all files (`index.php`, `invoice.php`, `css/`, `js/`) to your server's web root (e.g., `public_html`).
2. Ensure the web server has permissions to read the files.
3. Access the application via your domain name.

### Docker Deployment (Optional):

You can also use a simple Docker setup with an official PHP-Apache image:

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
