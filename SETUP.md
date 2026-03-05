# 🛒 Santosh Store — Setup & Launch Guide

## Requirements
- PHP ≥ 8.2 + Composer
- MySQL ≥ 8.0 (running)
- Node.js ≥ 18 + npm

## 1. Configure Database
Open `.env` and update your MySQL credentials:
```
DB_DATABASE=santosh_store
DB_USERNAME=root
DB_PASSWORD=your_password
```

## 2. Create the Database
In MySQL:
```sql
CREATE DATABASE santosh_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 3. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```
This creates all tables and inserts:
- Admin user: `admin@santoshstore.com` / `password123`
- 10 sample customers
- 10 categories, 50 Indian grocery products
- Store settings and sample reviews

## 4. Link Storage for Images
```bash
php artisan storage:link
```

## 5. Start Dev Server
```bash
# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite frontend (hot reload)
npm run dev
```
Then visit: **http://localhost:8000**

## 6. Admin Panel
Go to: **http://localhost:8000/admin**
Login: `admin@santoshstore.com` / `password123`

---

## Build for Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Structure
```
app/
  Http/
    Controllers/
      Public/   → Home, Product, About, Contact
      User/     → Dashboard, Cart, Checkout, Address, Favorite, Review
      Admin/    → Dashboard, Product, Category, Order, Customer, Review, Settings
    Middleware/
      AdminMiddleware.php
  Models/       → User, Role, Product, Category, Order, OrderItem, Review, Favorite, Address, StoreSetting
  Repositories/ → ProductRepository, OrderRepository
  Services/     → CartService, OrderService, ImageService
  Policies/     → OrderPolicy, ReviewPolicy, AddressPolicy

resources/js/
  Layouts/      → GuestLayout, AdminLayout, UserDashboardLayout
  Components/   → CartDrawer, ProductCard
  Pages/
    Welcome.vue
    About.vue
    Contact.vue
    Checkout.vue
    Products/   → Index.vue, Show.vue
    Dashboard/  → Profile.vue, Orders.vue, Favorites.vue, Addresses.vue
    Admin/      → Dashboard.vue, Settings.vue
    Admin/Products/ → Index.vue, Create.vue
    Admin/Orders/   → Index.vue, Show.vue
    Admin/Reviews/  → Index.vue
  stores/       → cartStore.js, notificationStore.js
```
