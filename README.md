# 🛒 Reza Laravel POS

<p align="center">
  <img src="public/assets/img/logo.png" width="120" alt="Reza POS Logo">
</p>

<p align="center">
  <strong>An Enterprise-Grade, Modern Point of Sale (POS) and Inventory Management System built with Laravel 12 & NiceAdmin Bootstrap 5.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-blue.svg" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Database-MySQL-orange.svg" alt="MySQL">
  <img src="https://img.shields.io/badge/Auth-Breeze-blueviolet.svg" alt="Laravel Breeze">
  <img src="https://img.shields.io/badge/RBAC-Spatie_Permission-green.svg" alt="Spatie Permission">
  <img src="https://img.shields.io/badge/Tests-50_passed_/_189_assertions-brightgreen.svg" alt="Tests 50 passed">
  <img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="MIT License">
</p>

---

## 🌟 Key Features

### 1. 💻 Fast & Intuitive POS Terminal
- **Live Barcode Scanner Support:** Auto-focus barcode input for instant scan-to-cart workflow.
- **Dynamic Category Filtering & Search:** Real-time search across products by name or code.
- **Interactive Cart & Real-Time Math:** Quantity adjustments, flat or percentage item/cart discounts, configurable VAT/Tax, and net pay-able computation.
- **Hold / Park Sales:** Suspend active customer carts when lines are busy, serve subsequent customers, and retrieve parked sales in one click.
- **Multi-Method & Split Payments:** Accept Cash, Card, Mobile Banking (bKash/Nagad), Credit (due sales), or Split payments across multiple methods.
- **Quick Customer Modal:** Register new customers on-the-fly via AJAX without leaving the active sales terminal.

### 2. 🧾 Receipts & Invoicing
- **80mm & 58mm Thermal POS Receipts:** Specially formatted for thermal receipt printers (`@media print` optimized) with store branding, itemized table, cashier info, VAT, change amount, Code-128 barcode SVG, and auto-print trigger (`?print=1`).
- **Standard A4 Tax Invoices:** Official letterhead, customer billing info, cashier shift details, itemized table, total in words, notes, and authorization signatures.

### 3. 📦 Product Catalog & Barcode Generator
- **Comprehensive Product Management:** Cost price, selling price, units of measure, categories, image uploads, and minimum alert thresholds.
- **Multi-Label Barcode Sheet Generator:** Select print quantity per item, label height/width, and generate full A4 sheets with printable Code-128 barcodes.

### 4. 🚚 Procurement & Stock Inflow
- **Suppliers Directory:** Contact info, company address, and procurement history.
- **Stock Purchases:** Purchase orders automatically increment inventory stock quantities and update average cost price (COGS).
- **Printable Purchase Bills:** Official bill copy for inward inventory receipts.

### 5. 💰 Cash Register Shift Auditing
- **Shift Management:** Cashiers enter opening cash float before initiating sales.
- **Real-Time Drawer Tracking:** Tracks real-time cash sales during the active shift.
- **Closing & Discrepancy Audit:** Cash counting at shift closure calculates variance (exact match, surplus, or cash shortage) for fraud prevention.
- **Shift History Logs:** Complete historical log of all opened, closed, and audited drawer sessions.

### 6. 👥 Customer Management & Due Ledger
- **Walk-in & Regular Customers:** Separate default walk-in customer from account-based credit clients.
- **Customer Due Ledger:** Track outstanding receivables, record partial due settlements, and view payment transaction logs.

### 7. 📊 Reports & Business Analytics
- **Sales Analytics:** Filter by date presets (Today, Yesterday, This Week, This Month, Custom Date Range), view daily trend tables, revenue, gross profit, and payment method mix.
- **Profit & Loss Statement (COGS):** Calculates real-time Cost of Goods Sold (COGS), gross profit, and margin percentages. Highlights category-wise profit contribution and top 10 most profitable items.
- **Stock Valuation Report:** Valuation at cost (invested capital) vs valuation at retail (expected turnover), unrealized potential profit, with **Low Stock** and **Out of Stock** alert badges.
- **Customer Due Receivables Report:** Outstanding market dues and aging analysis.

### 8. ⚙️ Store Settings & Staff Management
- **Store Profile:** Company name, hotline, official email, physical address, BIN/VAT registration number, and company logo.
- **POS Configuration:** Currency symbol (e.g., `৳`), currency code (`BDT`), default VAT %, invoice prefix (`INV-`), and custom receipt footer messages.
- **Staff Administration (Admin Only):** Create, update, or deactivate cashiers and managers, assign roles, and securely reset passwords.
- **Roles & Permissions Matrix:** Visual inspection of RBAC permissions across all modules.

---

## 🔐 Default Demo Accounts

All accounts are pre-configured via database seeders with the password `password`:

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@gmail.com` | `password` | Complete access: POS, Inventory, Finance, Reports, Settings, Staff |
| **Manager** | `manager@gmail.com` | `password` | Inventory, Procurement, Products, Orders, Reports |
| **Cashier** | `cashier@gmail.com` | `password` | POS Terminal, Cash Drawer Shifts, Receipts |

---

## 🛠️ Technology Stack

- **Backend Framework:** [Laravel 12.x](https://laravel.com)
- **Language:** PHP 8.2+
- **Database:** MySQL
- **Authentication:** [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze) (Blade Stack)
- **RBAC:** [Spatie Laravel Permission v6](https://spatie.be/docs/laravel-permission)
- **UI Architecture:** [NiceAdmin](https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/) Bootstrap 5 Template (fully integrated Blade layouts)
- **Barcode Engine:** [picqer/php-barcode-generator](https://github.com/picqer/php-barcode-generator)
- **Testing Suite:** Pest PHP & PHPUnit

---

## 🚀 Installation & Setup

### 1. Clone Repository
```bash
git clone https://github.com/Tauhid219/Reza-Laravel-POS.git
cd Reza-Laravel-POS
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reza_laravel_pos
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Migration & Seeding
Run migrations and populate roles, units, categories, suppliers, sample products with barcodes, settings, and demo users:
```bash
php artisan migrate --seed
```

### 5. Storage Symlink
```bash
php artisan storage:link
```

### 6. Run Application
```bash
php artisan serve
```
Navigate to `http://127.0.0.1:8000` in your web browser.

---

## 🧪 Automated Test Suite

The application is thoroughly covered by comprehensive feature tests:
```bash
php artisan test
```

### Test Coverage Summary:
- **Auth:** Authentication, registration, password reset, email verification.
- **Product & Inventory:** Products CRUD, stock updates, barcode sheets, categories, units.
- **Procurement:** Suppliers CRUD, purchase orders, stock incrementation.
- **Cash Register:** Shift opening float, live cash tracking, shift closure, and variance audit.
- **POS & Checkout:** Atomic checkout transactions, stock deduction, COGS calculation, customer credit updates, thermal receipts, A4 invoices.
- **Reports:** Sales period filtering, Profit & Loss statement, stock valuation, customer receivables.
- **Settings & Staff:** Store configuration updates, cashier creation, role protection.

```text
Tests:    50 passed (189 assertions)
Duration: ~5s
```

---

## 📄 License

This open-source software is licensed under the [MIT License](LICENSE).
