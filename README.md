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
9. [Routes](#routes)
10. [License](#license)

## About LaraStore

LaraStore is a comprehensive e-commerce application built with Laravel 12. It provides a complete online shopping solution with both a user-facing storefront and a powerful admin panel. The application implements modern e-commerce features including product management, cart functionality, checkout process, order management, and coupon system.

## Features

### Public Storefront
- **Home Page**: Featured products and categories
- **Product Catalog**: Filterable and sortable product listings with pagination
- **Product Details**: Individual product pages with detailed information
- **Shopping Cart**: Persistent cart functionality for authenticated users
- **Wishlist**: Ability to save favorite products for later
- **Search & Filters**: Category filtering and multiple sorting options
- **Product Reviews**: User-generated reviews and ratings system
- **FAQ Page**: Frequently asked questions section
- **Contact Page**: Contact form with thank you page
- **Policies Page**: Terms of service, privacy policy, and other legal documents

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
- **Persistent Cart**: Database-stored cart for logged-in users
- **Cart Operations**: Add, update quantity, and remove items
- **Coupon System**: Apply discount codes with validation
- **Wishlist Integration**: Add/remove items from wishlist
- **Secure Checkout Flow**: Multi-step checkout process with shipping and payment
- **Payment Processing**: Stripe integration via Laravel Cashier

### Technical Features
- **Queue System**: Background job processing
- **Responsive Design**: Mobile-first design using Tailwind CSS
- **Advanced Security**: Comprehensive security features including CSRF protection, input validation, authentication, and additional security measures


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
│   │   ├── Auth/     # Authentication controllers
│   │   └── Shop/     # Public storefront controllers
│   ├── Middleware/   # Custom middleware
│   └── Requests/     # Form request validation
├── Jobs/             # Queueable jobs
├── Listeners/        # Event listeners
├── Mail/             # Mail classes
├── Models/           # Eloquent models
├── Notifications/    # Notification classes
├── Observers/        # Model observers
├── Providers/        # Service providers
└── View/             # View composers and helpers
```

### Controller Organization
- **Admin Controllers**: Separate controllers for admin panel functionality
- **Shop Controllers**: Controllers for public-facing features
- **Auth Controllers**: Authentication-related controllers
- **Resource Controllers**: Standard CRUD operations for entities

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
- Access to FAQ and Contact pages
- Proceed to checkout (with required account creation)

## Routes

### Public Routes
- `GET /` - Home page
- `GET /shop` - Product listing
- `GET /product/{product:slug}` - Individual product page
- `GET /faq` - FAQ page
- `GET /contact-us` - Contact page
- `POST /contact-us` - Send contact form
- `GET /contact-us/thank-you` - Thank you page after contact form
- `GET /policies/{policy}` - Policies pages (terms, privacy, etc.)

### Authenticated Routes
- `GET /dashboard` - User dashboard
- `GET /profile` - Profile management
- `PATCH /profile` - Update profile
- `DELETE /profile` - Delete profile
- `POST /cart/add` - Add item to cart
- `PATCH /cart/update` - Update cart item quantity
- `DELETE /cart/remove` - Remove item from cart
- `GET /cart` - View cart
- `POST /cart/apply-coupon` - Apply discount code
- `POST /cart/remove-coupon` - Remove discount code
- `POST /wishlist/{product}` - Toggle wishlist item
- `DELETE /wishlist/{product}` - Remove from wishlist

### Account Routes (Authenticated)
- `GET /account` - Account dashboard
- `GET /account/orders` - Order history
- `GET /account/orders/{order}` - Order details
- `GET /account/wishlist` - Wishlist

### Checkout Routes (Authenticated)
- `GET /checkout` - Checkout review
- `GET /checkout/shipping` - Shipping information
- `POST /checkout/shipping` - Store shipping info
- `GET /checkout/payment` - Payment selection
- `POST /checkout/payment` - Process payment
- `POST /checkout/create-payment-intent` - Create Stripe payment intent
- `GET /checkout/success/{order}` - Order confirmation

### Admin Routes (Authenticated & Admin Role)
- `GET /admin/dashboard` - Admin dashboard
- `GET/POST/PUT/PATCH/DELETE /admin/products` - Product management (resource routes)
- `GET/POST/PUT/PATCH/DELETE /admin/categories` - Category management (resource routes)
- `GET/POST/PUT/PATCH/DELETE /admin/users` - User management (resource routes)
- `PUT /admin/users/{user}/toggle-status` - Toggle user status
- `GET/POST/PUT/PATCH/DELETE /admin/orders` - Order management (resource routes)
- `PUT /admin/orders/{order}/status` - Update order status
- `GET /admin/marketing/coupons` - Coupon management index
- `GET /admin/marketing/coupons/create` - Create coupon
- `POST /admin/marketing/coupons` - Store coupon
- `GET /admin/marketing/coupons/{coupon}/edit` - Edit coupon
- `PUT /admin/marketing/coupons/{coupon}` - Update coupon
- `DELETE /admin/marketing/coupons/{coupon}` - Delete coupon
- `GET /admin/marketing/reports` - Analytics reports index
- `GET /admin/marketing/reports/stock` - Stock reports
- `GET /admin/marketing/reports/sales-data` - Sales data
- `GET /admin/marketing/reports/conversion-rate` - Conversion rate
- `GET /admin/marketing/reports/stock-data` - Stock data
- `GET /admin/marketing/inventory` - Inventory management
- `POST /admin/marketing/inventory/bulk-update-stock` - Bulk update stock
- `POST /admin/marketing/inventory/bulk-update-status` - Bulk update status
- `GET /admin/marketing/inventory/export` - Export products
- `POST /admin/marketing/inventory/import` - Import products
- `GET /admin/notifications` - Notification management index
- `GET /admin/notifications/unread` - Unread notifications
- `PUT /admin/notifications/{id}/read` - Mark notification as read
- `PUT /admin/notifications/mark-all-read` - Mark all notifications as read
- `DELETE /admin/notifications/{id}` - Delete notification
- `GET /admin/reviews` - Review management index
- `GET /admin/reviews/pending` - Pending reviews
- `GET /admin/reviews/approved` - Approved reviews
- `PUT /admin/reviews/{id}/approve` - Approve review
- `PUT /admin/reviews/{id}/toggle-approval` - Toggle review approval
- `DELETE /admin/reviews/{id}` - Reject review

## Abandoned Cart Recovery System

### Overview
The Abandoned Cart Recovery System is designed to re-engage customers who leave items in their shopping cart without completing their purchase. This feature automatically detects abandoned carts and sends personalized email reminders to customers at predetermined intervals to encourage them to complete their purchase.

### Business Impact
- Helps recover 10-30% of lost sales from cart abandonment (industry average shows 70% abandonment rate)
- Increases revenue with minimal additional cost
- Improves customer retention through follow-up engagement
- Enhances customer experience with convenient reminders

### Key Features

#### 1. Automatic Cart Tracking
- Detects when a user adds items to cart but doesn't complete checkout
- Records timestamps of last cart activity
- Automatically marks carts as abandoned after a configurable threshold (default: 1 hour)

#### 2. Multi-Touch Email Campaign
- **First Reminder**: Sent 1 hour after cart abandonment
- **Second Reminder**: Sent 24 hours after first reminder with 5% discount offer
- **Third Reminder**: Sent 3 days after second reminder with 10% discount offer
- All timing intervals and discount percentages are configurable by admin

#### 3. Personalized Content
- Email templates include actual cart items
- Personalized greetings and messages
- Clear call-to-action buttons to return to checkout
- Discount codes automatically applied in later emails

#### 4. Administrative Controls
- **Dashboard**: Visual statistics showing total abandoned carts, notifications sent, and conversion rates
- **Configuration**: Admin can customize reminder intervals and discount percentages
- **Template Customization**: Admin can modify email subject lines and body content
- **Analytics**: Monitor recovery statistics and opt-out rates

#### 5. Performance Optimized
- Database indexes for efficient querying of abandoned carts
- Queue-based processing for scalable email delivery
- Proper error handling and logging

### How It Works

1. **Cart Abandonment Detection**: The system monitors customer activity and identifies carts that have been inactive for longer than the configured threshold.

2. **Schedule Processing**: A scheduled job runs every 30 minutes to identify new abandoned carts and process scheduled reminders.

3. **Notification Sending**: For eligible carts, the system dispatches notification jobs to send personalized email reminders.

4. **Tracking**: All notifications are logged in the database to track open rates, click-through rates, and conversions.

5. **Recovery**: Successful conversions from abandoned cart emails are recorded and reflected in admin analytics.

### Admin Interface

#### Dashboard Access
- Navigate to "Abandoned Cart Analytics" in the Marketing section of the admin panel
- View real-time statistics including:
  - Total abandoned carts
  - Notifications sent
  - Recovered carts
  - Conversion rate percentage
  - Notification breakdown by type
  - Recent abandoned carts with items information

#### Configuration Access
- Navigate to "Abandoned Cart Settings" in the Marketing section
- Customize the following settings:
  - First reminder timing (hours after abandonment)
  - Second reminder timing (hours after first reminder)
  - Third reminder timing (days after second reminder)
  - Abandoned cart threshold (minutes of inactivity)
  - Discount percentages for second and third reminders
  - Edit email subject lines and body content for all three reminder types

### Environment Variables
The system supports the following environment variables for easy configuration:
```
ABANDONED_CART_FIRST_REMINDER_HOURS=1
ABANDONED_CART_SECOND_REMINDER_HOURS=24
ABANDONED_CART_THIRD_REMINDER_DAYS=3
ABANDONED_CART_THRESHOLD_MINUTES=60
ABANDONED_CART_FIRST_DISCOUNT=0
ABANDONED_CART_SECOND_DISCOUNT=5
ABANDONED_CART_THIRD_DISCOUNT=10
```

### Technical Architecture

#### Database Schema
- `abandoned_cart_notifications` table stores notification history
- Extended `carts` table with `abandoned_at`, `remind_at`, and `notified_count` columns
- `abandoned_cart_settings` table for admin-configured settings

#### Core Components
- `ProcessAbandonedCarts` job: Periodically identifies and processes abandoned carts
- `SendAbandonedCartNotification` job: Sends individual notifications
- Multiple notification classes for each reminder type
- Custom email templates for each stage

#### Queue System
- Uses Laravel's queue system for efficient notification processing
- Database queue driver recommended for reliability
- Queue workers process notifications asynchronously

### Expected Outcomes
- 10-20% recovery rate of abandoned carts
- 5-15% increase in overall revenue
- Improved customer engagement and retention
- Better understanding of customer behavior patterns

### Monitoring Success Metrics
- Track number of abandoned carts and recoveries
- Monitor email open and click-through rates
- Measure conversion rate from abandoned cart emails
- Track revenue generated from recovered carts
- Monitor opt-out rates for continuous improvement

The abandoned cart recovery system is designed to seamlessly integrate with your existing e-commerce platform and provide immediate value in recovering lost sales while maintaining a positive customer experience.

## License

The LaraStore application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">&copy; 2025 LaraStore. All rights reserved.</p>