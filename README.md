docs\sulong-kalinga\web\README.md
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sulong Kalinga - Installation Guide (Web)

### Prerequisites
- XAMPP
- Composer
- Git
- PostgreSQL
- Visual Studio Code (recommended)

### Installation Steps

#### 1. Install Required Software

- **XAMPP**: Download and install from [https://www.apachefriends.org/](https://www.apachefriends.org/)

- **Composer**: Download from [https://getcomposer.org/download/](https://getcomposer.org/download/)
  - During installation, ensure you select the correct PHP path
  - Navigate to your XAMPP folder > php > php.exe

- **Git**: Download from [https://git-scm.com/downloads](https://git-scm.com/downloads) if you don't already have it

- **PostgreSQL**: Download from [https://www.postgresql.org/](https://www.postgresql.org/) if you don't already have it
  - For simplicity, you can create a folder for PostgreSQL inside your XAMPP folder as its installation directory
  - Use the team's uniform password for postgres superuser (if it's your first installation)
  - The default port (5432) will work fine

#### 2. Configure PHP Extensions

Edit the php.ini file in your XAMPP > php folder:
- Open the file in Notepad or any text editor
- Remove the semicolon (;) before the following extensions:
  ```
  extension=pdo_pgsql
  extension=pdo_sqlite
  extension=pgsql
  ```

#### 3. Setup PostgreSQL Database

1. Open pgAdmin (or your preferred PostgreSQL interface)
2. Register a new server:
   - Navigate to Register > Server
   - On the General tab, enter **SulongKalingaDBServer** as the name
   - On the Connection tab, keep default settings but use the team's password
   - Click Save
3. Create a new database:
   - Right-click on SulongKalingaDBServer
   - Hover over Create > Database then click
   - For General > Database, enter: **sulong_kalinga_db**
   - Click Save

#### 4. Project Setup

1. Open the web folder of the repository in Visual Studio Code
2. Download the .env file from the team OneDrive folder and place it inside the web folder
3. Open terminal in VSC and run:
   ```
   composer install
   ```
4. After completion, run:
   ```
   composer run dev
   ```
5. Initialize the database:
   ```
   php artisan migrate:refresh
   ```
6. Seed the database:
   ```
   php artisan db:seed
   ```

#### 5. Running the Application

To start the web server:
```
php artisan serve
```

You can now access the website through localhost, typically at [http://localhost:8000](http://localhost:8000)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).