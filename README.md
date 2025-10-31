# Laravel Project

A web application built with Laravel framework.

## Requirements

- PHP >= 8.1
- Composer
- SQLite or MySQL
- Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# OR for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

7. Run database migrations:
```bash
php artisan migrate
```

8. (Optional) Seed the database:
```bash
php artisan db:seed
```

## Development

Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Building Assets

Compile frontend assets:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Testing

Run the test suite:
```bash
php artisan test
```

## Code Style

Format code with Laravel Pint:
```bash
./vendor/bin/pint
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Additional Information

For more information about Laravel, visit the [official documentation](https://laravel.com/docs).
