# Backend - Blog API (Laravel)

## Overview

This is the backend for the blog application, built using **Laravel**. It provides a RESTful API with authentication, CRUD operations for blog posts, category filtering, search, pagination, and image uploads.

---

## Setup

### Prerequisites

-   **PHP 8.3**

-   **Composer**

-   **MySQL or SQLite (for testing)**

-   **Laravel 10+**

### Installation Steps

1.  **Clone the repository**

    ```
    git clone https://github.com/your-repo/blog-app.git
    cd blog-app/backend
    ```

2.  **Install dependencies**

    ```
    composer install
    ```

3.  **Create the** `**.env**` **file**

    ```
    cp .env.example .env
    ```

4.  **Configure the** `**.env**` **file** Open `.env` and update the database settings:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=blog_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate application key**

    ```
    php artisan key:generate
    ```

6.  **Run migrations & seed database**

    ```
    php artisan migrate --seed
    ```

7.  **Set up Laravel Sanctum**

    ```
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan migrate
    ```

8.  **Start Laravel server**

    ```
    php artisan serve
    ```

    Your backend API is now running at `http://127.0.0.1:8000`.

---

## Authentication (Laravel Sanctum)

### Sanctum Configuration

1.  **Ensure Sanctum middleware is included in** `**api.php**`

    ```
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
    ```

2.  **Enable CORS in** `**cors.php**`

    ```
    'supports_credentials' => true,
    ```

3.  **Update** `**.env**` **for Sanctum**

    ```
    SANCTUM_STATEFUL_DOMAINS=localhost:8080
    SESSION_DOMAIN=localhost
    ```

4.  **Create a** `**.env.testing**` **for running tests without affecting production**

    ```
    cp .env .env.testing
    ```

    **Update** `**.env.testing**` **to use SQLite:**

    ```
    DB_CONNECTION=sqlite
    DB_DATABASE=:memory:
    ```

---

## Running Tests

To run API tests:

```
php artisan test
```

---

## API Endpoints

| Method | Endpoint | Description |
| `POST` | `/api/login` | User login |
| `POST` | `/api/logout` | Logout |
| `GET` | `/api/posts` | Fetch paginated posts |
| `GET` | `/api/posts/{slug}` | Get single post |
| `POST` | `/api/posts` | Create post (auth required) |
| `PUT` | `/api/posts/{slug}` | Update post (auth required) |
| `DELETE` | `/api/posts/{slug}` | Delete post (auth required) |

---
There is a Postman collection export for API testing at `blog-backend\public\Blog REST- AUTH, CRUD, & variable.postman_collection.json`
