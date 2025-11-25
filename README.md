# LaraStore - E-commerce Application

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Table of Contents

1. [About LaraStore](#about-larastore)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Database Schema](#database-schema)
5. [Application Architecture](#application-architecture)
6. [Installation](#installation)
7. [Project Structure](#project-structure)
8. [User Roles](#user-roles)
9. [API Endpoints](#api-endpoints)
10. [Contributing](#contributing)
11. [Security](#security)
12. [License](#license)

## About LaraStore

LaraStore is a comprehensive e-commerce application built with Laravel 12. It provides a complete online shopping solution with both a user-facing storefront and a powerful admin panel. The application implements modern e-commerce features including product management, cart functionality, checkout process, order management, and coupon system.

## Features

### Public Storefront
- **Home Page**: Hero section, featured categories, and new arrivals section with caching
- **Product Catalog**: Filterable and sortable product listings with pagination
- **Product Details**: Individual product pages with image gallery, description, reviews, and related products
- **Shopping Cart**: Persistent cart functionality (both authenticated users and guest sessions)
- **Wishlist**: Ability to save favorite products for later
- **Search & Filters**: Category filtering, price range filtering, and multiple sorting options
- **Product Reviews**: User-generated reviews and ratings system

### User Account Features
- **Order History**: Track and view all past orders
- **Wishlist Management**: View and manage saved products
- **Profile Management**: Update personal information and account settings
- **Order Details**: Detailed view of each order with shipping information

### Admin Panel
- **Dashboard**: Real-time sales analytics, recent orders, and low stock alerts
- **Product Management**: Full CRUD functionality for products with image uploads
- **Category Management**: Organize products into categories
- **Order Management**: View, update status, and manage all orders
- **User Management**: Manage customer accounts and view their order history
- **Marketing Tools**:
  - **Coupon Management**: Create and manage discount codes (fixed amount or percentage)
  - **Analytics Reports**: Sales data, stock reports, and conversion rates
- **Inventory Management**: Bulk update stock and product status
- **Review Management**: Moderate and approve product reviews
- **Notification System**: Admin notifications for various events

### Shopping & Checkout
- **Persistent Cart**: Database-stored cart for logged-in users and session-based for guests
- **Cart Operations**: Add, update quantity, and remove items with AJAX support
- **Coupon System**: Apply discount codes with validation (minimum amount, expiration, usage limits)
- **Wishlist Integration**: Move items between cart and wishlist
- **Secure Checkout Flow**: Multi-step checkout process with guest support
- **Payment Processing**: Stripe integration via Laravel Cashier
- **Order Finalization**: Database transactions to ensure data consistency

### Technical Features
- **Caching**: Optimized performance with strategic caching of frequently accessed data
- **Database Transactions**: Ensures data integrity during order processing
- **Queue System**: Background job processing for email notifications
- **Responsive Design**: Mobile-first design using Tailwind CSS
- **SEO Optimized**: Proper meta tags and URL structures
- **Security**: CSRF protection, input validation, and authentication

## Technology Stack

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Authentication**: Laravel Breeze
- **Payment Processing**: Laravel Cashier (Stripe)
- **Queue System**: Database queues
- **Caching**: Laravel caching with various backends
- **File Storage**: Laravel Storage system for product images

### Frontend
- **Template Engine**: Blade (Server-Side Rendering)
- **CSS Framework**: Tailwind CSS with Forms plugin
- **JavaScript**: Alpine.js for interactivity
- **Build Tool**: Vite
- **HTTP Client**: Axios

### Database
- **Primary Database**: MySQL 8.0+ or PostgreSQL
- **Migrations**: Laravel migration system
- **ORM**: Eloquent

### Dependencies
- **Dependency Manager**: Composer
- **Frontend Package Manager**: NPM

## Database Schema

### Core Tables
- **`users`**: Customer and admin user accounts with role-based access
- **`categories`**: Product categorization system
- **`products`**: Comprehensive product information including pricing, inventory, and images
- **`orders`**: Order records with status tracking and payment information
- **`order_items`**: Individual items within orders with historical pricing
- **`carts`**: Persistent shopping cart for authenticated users
- **`cart_items`**: Individual items within user carts
- **`wishlists`**: User-saved products for later purchase
- **`reviews`**: Product ratings and customer reviews
- **`coupons`**: Discount code management with validation rules
- **`notifications`**: Admin notification system
- **`subscriptions`**: Payment subscription data from Laravel Cashier

### Key Features of Schema Design
- All monetary values stored in cents to avoid floating-point errors
- JSON fields for flexible data storage (shipping addresses)
- Indexed fields for optimized query performance
- Soft deletes where appropriate
- Foreign key constraints for data integrity

## Application Architecture

### Directory Structure
```
app/
├── Console/          # Artisan commands
├── Events/           # Event classes
├── Http/
│   ├── Controllers/
│   │   ├── Admin/    # Admin panel controllers
│   │   └── Shop/     # Public storefront controllers
│   ├── Middleware/   # Custom middleware
│   └── Requests/     # Form request validation
├── Jobs/             # Queueable jobs
├── Models/           # Eloquent models
├── Notifications/    # Notification classes
├── Providers/        # Service providers
└── Services/         # Business logic services
```

### Controller Organization
- **Admin Controllers**: Separate controllers for admin panel functionality
- **Shop Controllers**: Controllers for public-facing features
- **Resource Controllers**: Standard CRUD operations for entities
- **API Controllers**: RESTful API endpoints

### Model Relationships
- **Product ↔ Category**: Many-to-one relationship
- **Product ↔ OrderItem**: One-to-many relationship
- **User ↔ Order**: One-to-many relationship
- **User ↔ Review**: One-to-many relationship
- **User ↔ Wishlist**: One-to-many relationship
- **Coupon ↔ Order**: Many-to-one relationship (optional)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/larastore.git
   cd larastore
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   - Configure your database in `.env`
   - Run migrations:
     ```bash
     php artisan migrate
     ```

6. **Database Seeding (Optional)**
   - Seed the database with sample data:
     ```bash
     php artisan db:seed
     ```
   - The main DatabaseSeeder creates:
     - 2 users (1 regular user and 1 admin user)
     - 15 product categories
     - 10 sample products with realistic names, descriptions, and pricing
   - Or run specific seeders (currently uses a single DatabaseSeeder that creates all sample data)

7. **Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Serve the Application**
   ```bash
   php artisan serve
   ```

### Using the Setup Script
Alternatively, use the provided setup script:
```bash
composer run setup
```

## Project Structure

### Key Files and Directories
- `app/` - Application logic including Models, Controllers, Services
- `bootstrap/` - Framework bootstrapping
- `config/` - Configuration files
- `database/` - Migrations, seeds, and factories
- `public/` - Publicly accessible files (images, CSS, JS)
- `resources/` - Views, raw assets, and language files
- `routes/` - Application route definitions
- `storage/` - File storage and compiled views
- `tests/` - Test cases

### View Organization
- `resources/views/shop/` - Public storefront views
- `resources/views/admin/` - Admin panel views
- `resources/views/layouts/` - Base templates
- `resources/views/components/` - Blade components

## User Roles

### Customer
- Browse and search products
- Add products to cart and wishlist
- Place orders using various payment methods
- View order history and details
- Write product reviews
- Manage personal profile

### Admin
- Access to the admin dashboard
- Full product and category management
- Order processing and status updates
- Customer account management
- Marketing tool access (coupons, reports)
- Review moderation
- Inventory management

### Guest User
- Browse products and view details
- Add items to cart (session-based)
- Proceed to checkout (with optional account creation)

## API Endpoints

### Public Routes
- `GET /` - Home page
- `GET /shop` - Product listing
- `GET /product/{slug}` - Individual product page

### Authenticated Routes
- `POST /cart/add` - Add item to cart
- `PATCH /cart/update` - Update cart item quantity
- `DELETE /cart/remove` - Remove item from cart
- `GET /cart` - View cart
- `POST /cart/apply-coupon` - Apply discount code
- `POST /cart/remove-coupon` - Remove discount code
- `POST /wishlist/{product}` - Toggle wishlist item
- `DELETE /wishlist/{product}` - Remove from wishlist

### Checkout Routes
- `GET /checkout` - Checkout review
- `GET /checkout/shipping` - Shipping information
- `POST /checkout/shipping` - Store shipping info
- `GET /checkout/payment` - Payment selection
- `POST /checkout/payment` - Process payment
- `POST /checkout/create-payment-intent` - Create Stripe payment intent
- `GET /checkout/success/{order}` - Order confirmation

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET/POST/PUT/DELETE /admin/products` - Product management
- `GET/POST/PUT/DELETE /admin/categories` - Category management
- `GET/POST/PUT/DELETE /admin/orders` - Order management
- `GET/POST/PUT/DELETE /admin/users` - User management
- `GET/POST/PUT/DELETE /admin/marketing/coupons` - Coupon management
- `GET /admin/marketing/reports` - Analytics reports
- `GET/POST/PUT/DELETE /admin/notifications` - Notification management
- `GET/POST/PUT/DELETE /admin/reviews` - Review management

## Contributing

Thank you for considering contributing to LaraStore! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Ensure all tests pass (`php artisan test`)
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Development Workflow
- Use PSR-12 coding standards
- Write tests for new features
- Document public methods and classes
- Follow the existing code style

## Security

LaraStore takes security seriously. If you discover any security vulnerabilities, please send an email to [rishabh.78275@gmail.com](mailto:rishabh.78275@gmail.com) instead of using the issue tracker.

### Security Features Implemented
- CSRF protection on all forms
- Input validation and sanitization
- SQL injection prevention through Eloquent ORM
- XSS prevention through Blade template escaping
- Secure password hashing
- Authentication middleware
- Role-based access control

## License

The LaraStore application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">&copy; 2025 LaraStore. All rights reserved.</p>