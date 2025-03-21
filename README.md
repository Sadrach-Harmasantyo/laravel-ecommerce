# Laravel E-Commerce

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square&logo=laravel) ![Livewire](https://img.shields.io/badge/Livewire-3-blue?style=flat-square&logo=laravel) ![Filament](https://img.shields.io/badge/Filament-3-green?style=flat-square&logo=filament) ![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3-06B6D4?style=flat-square&logo=tailwind-css) ![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap)

## About the Project

This repository showcases a **modern e-commerce application** built with the latest web technologies:

- **Laravel 11**: A robust PHP framework for building scalable web applications.
- **Livewire 3**: A powerful library for building dynamic frontends using server-driven rendering.
- **Filament 3**: An elegant admin panel framework to manage the application backend efficiently.
- **Tailwind CSS**: A utility-first CSS framework for creating responsive and modern UI designs.
- **MySQL**: A popular relational database management system.
- **Bootstrap**: A popular front-end framework for building responsive and mobile-first websites.

### Features

- **Scalable and Maintainable**: Built using the latest version of Laravel for optimal performance and scalability.
- **Dynamic UI**: The frontend is powered by Livewire 3, providing real-time, interactive user experiences.
- **Powerful Admin Panel**: With Filament 3, managing the store's content, orders, and products is seamless.
- **Responsive Design**: Tailwind CSS ensures the application looks great on all screen sizes.
- **Database Integration**: MySQL is used for efficient data storage and retrieval.
- **E-commerce Functionality**: The application includes features like product listings, shopping cart, and checkout.
- **User Authentication**: User registration and login functionality.
- **User Profiles**: Users can view their order history and profile details.
- **Notifications Admin**: Notifications for new orders.

---

## Pages Completed (Frontend)

The following pages have been developed using **Livewire 3**:

1. **About Page**: Provides information about the store.
2. **Blog Page**: Displays blog posts.
3. **Blog Details Page**: Shows detailed information about a blog post.
4. **Brands Page**: Lists available brands.
5. **Cart Page**: Displays the user's cart items.
6. **Categories Page**: Showcases product categories.
7. **Checkout Page**: Handles the checkout process.
8. **Home Page**: The landing page with featured products and categories.
9. **My Orders Page**: Lists the user's order history.
10. **Order Details Page**: Provides detailed information about a specific order.
11. **Products Page**: Displays all available products.
12. **Product Details Page**: Shows information about a specific product.
13. **Profile Page**: Confirms successful orders.
13. **Success Page**: Confirms successful orders.

---

## Getting Started

Follow these instructions to set up and run the project locally.

### Prerequisites

Ensure you have the following installed:

- PHP >= 8.2
- Composer
- Node.js >= 16
- NPM or Yarn
- MySQL

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Sadrach-Harmasantyo/laravel-ecommerce.git
   cd laravel-ecommerce
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up the `.env` file:
   ```bash
   cp .env.example .env
   ```
   Update the database credentials and other environment variables as needed.

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

6. Build frontend assets:
   ```bash
   npm run dev
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

<!-- ### Testing

Run the following command to execute tests:
```bash
php artisan test
``` -->

---

## Roadmap

### Planned Features:
- Add product variant and stock management.

---
