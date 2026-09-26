# 🛒 Laravel Ecommerce Platform

A modern, full-featured Laravel 13 e-commerce system with a complete admin panel, dynamic payment methods, product variants, reviews, coupons, inventory management, and a responsive storefront — ready to sell as a white-label SaaS product.

![Laravel](https://img.shields.io/badge/Laravel-13.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/License-MIT-green)

---

## ✨ Features

### 🛒 Customer Storefront
- Home page with hero slider, featured products, categories, banners, new arrivals, best sellers
- Product catalog with search, filter (category, brand, price), sort
- Product detail page with image gallery, video, **product variants (size/color)**
- Shopping cart (session-based + AJAX)
- Coupon codes with validation
- Checkout for guests & registered users
- Register during checkout
- Order tracking with status timeline
- Customer dashboard (profile, password, addresses, orders)
- Cancel order (pending/confirmed only)
- Download invoice as PDF
- Product reviews & ratings
- Newsletter subscription
- Contact form
- WhatsApp floating button (admin toggle)

### 🔐 Admin Panel
- **Separate admin login** (`/admin/login`)
- Dashboard with real-time stats, sales chart, recent orders, low-stock warnings
- **Product CRUD** with 6 tabs — Basic / Pricing / Inventory / Shipping / Variants / SEO
- Category & Brand management
- **Attribute manager** — create Size, Color, Material + values with color swatches
- **Product variants** — auto-generate matrix (S×Red, S×Blue, M×Red…)
- Order management (status workflow, tracking, invoice PDF, packing slip)
- Customer management (list, view, **block/unblock**)
- Coupon management
- **Inventory tracking** with adjust + transaction history
- Low-stock alerts on dashboard
- Review moderation (approve/reject/reply)
- **Banners management** with schedule + toggle
- **CMS pages** (About, Privacy, Terms, Return, Shipping)
- **Payment methods** — admin creates any method from UI
- Newsletter subscribers + CSV export
- Contact messages inbox
- **Reports** — sales, products, customers, payments
- **Site settings** — logo, favicon, currency, shipping, tax, SEO, WhatsApp, contact info
- Everything is **dynamic** — no hardcoded values

### 💳 Payments
- 100% dynamic — admin adds any payment method
- COD + Bank Transfer by default
- Encrypted credentials per method
- Optional auto-send bank details on order
- Built for Stripe, PayPal, JazzCash, EasyPaisa, Razorpay, Paystack — add your own gateway class

### 🎨 Tech Highlights
- Tailwind CSS + Alpine.js
- Fully responsive (mobile, tablet, desktop)
- Multi-admin ready (Super Admin / Manager / Staff)
- SaaS-friendly — one install per client

### 🔍 SEO
- Per-page meta tags
- Sitemap.xml
- Robots.txt
- Google Analytics support

---

## 🛠️ Tech Stack

| Layer | Tech |
|-------|------|
| Backend | Laravel 13, PHP 8.2+ |
| Database | MySQL 8+ / MariaDB 10.4+ |
| Frontend | Tailwind CSS 3, Alpine.js 3 |
| Build | Vite |
| PDF | barryvdh/laravel-dompdf |
| Auth | Laravel Breeze (Blade) |

---

## 📋 Requirements

- PHP **8.2+**
- Composer **2.x**
- Node.js **18+** & NPM
- MySQL **8+** / MariaDB **10.4+**

---

## 🚀 Installation

```bash
# 1. Clone the repository
git clone https://github.com/YOUR-USERNAME/laravel-ecommerce.git
cd laravel-ecommerce

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Copy env file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Configure database in .env, then run:
php artisan migrate --seed

# 7. Create storage link
php artisan storage:link

# 8. Build frontend assets
npm run build

# 9. Start the server
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔑 Default Admin Login

```
URL:      http://localhost:8000/admin/login
Email:    admin@test.com
Password: password
```

> ⚠️ **Change the password immediately after first login.**

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # Admin panel controllers
│   │   ├── Auth/          # Breeze auth
│   │   └── Shop/          # Storefront controllers
│   ├── Middleware/
│   │   ├── IsAdmin.php
│   │   └── EnsureUserNotBlocked.php
├── Models/
├── Services/
│   ├── InventoryService.php
│   └── ShippingService.php
└── Mail/

resources/views/
├── layouts/              # Master layouts (shop + admin)
├── shop/                 # Storefront views
├── admin/                # Admin views
├── emails/               # Email templates
└── auth/                 # Breeze auth views

routes/
├── web.php               # All routes
└── auth.php              # Breeze routes
```

---

## 🎯 Common Commands

```bash
# Development with hot reload
npm run dev

# Build production assets
npm run build

# Reset database + seed demo data
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Create storage link
php artisan storage:link
```

---

## 🧪 Demo Data

The seeder creates:
- 24 demo products with images (Picsum)
- 6 categories, 8 brands
- 2 attributes (Size, Color) with values
- 2 banners (hero, promo)
- 5 CMS pages
- 1 admin + sample settings

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check the [issues page](https://github.com/YOUR-USERNAME/laravel-ecommerce/issues).

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📝 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

## 📧 Contact

- **Author:** Your Name
- **Email:** your@email.com
- **GitHub:** [@your-username](https://github.com/your-username)

---

## ⭐ Support

If you find this project helpful, please give it a ⭐ on GitHub!

---

## 🗺️ Roadmap

- [x] Product variants (size/color)
- [x] Multi-payment support
- [x] Reviews + moderation
- [x] Inventory tracking
- [x] Reports + CSV export
- [ ] Multi-vendor support
- [ ] 2FA for admin
- [ ] Multi-language
- [ ] Activity log
- [ ] Stripe / PayPal / JazzCash / EasyPaisa native integration

---

**Built with ❤️ using Laravel**