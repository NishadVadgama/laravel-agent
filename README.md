# Laravel Application with Blade & Authentication

This is a Laravel application configured with:
- **Blade templating engine** (no React/Vue/Inertia)
- **SQLite database** for data storage
- **Laravel Breeze** authentication scaffolding
- **Tailwind CSS** for styling

## Features

✅ Complete authentication system:
- User registration
- Login/Logout
- Password reset
- Email verification
- Profile management
- Password confirmation

✅ User Roles & Articles System:
- Two user levels: admin and regular users
- Articles management (title, slug, description, date, status)
- Role-based access control
- Admins can see all articles
- Users can only see their own articles
- Dummy data generation with Faker

✅ Pre-built Blade components and layouts
✅ SQLite database (no MySQL/PostgreSQL required)
✅ Modern UI with Tailwind CSS

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- SQLite extension enabled in PHP

## Installation

The application is already set up and ready to use. If you need to reset or reinstall:

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file (already done)
cp .env.example .env

# Generate application key (already done)
php artisan key:generate

# Create SQLite database (already done)
touch database/database.sqlite

# Run migrations (already done)
php artisan migrate

# Build frontend assets
npm run build
```

## Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve
```

Visit: `http://localhost:8000`

### With Herd (Already Configured)

Since you're using Laravel Herd, the application is automatically served at:
`http://llmdemo.test`

### Compile Assets for Development

```bash
# Watch for changes and recompile
npm run dev
```

## Available Routes

### Public Routes
- `/` - Welcome page
- `/login` - Login page
- `/register` - Registration page
- `/forgot-password` - Password reset request

### Protected Routes (requires authentication)
- `/dashboard` - User dashboard
- `/profile` - User profile management
- `/articles` - View articles (role-based access)
- `/articles/{id}` - View specific article

## Database

The application uses SQLite, with the database file located at:
```
database/database.sqlite
```

Default tables created:
- `users` - User accounts with roles (admin/user)
- `articles` - User articles with status tracking
- `password_reset_tokens` - Password reset tokens
- `sessions` - User sessions
- `cache` - Application cache
- `jobs` - Queue jobs
- `failed_jobs` - Failed queue jobs

### Seeded Data

The database comes pre-populated with dummy data:
- **Admin User**: `admin@example.com` / `password`
- **Test User**: `test@example.com` / `password`
- **10 Additional Users**: Random names/emails / `password`
- **~36 Articles**: Distributed across users with random content

See [ARTICLES_FEATURE.md](ARTICLES_FEATURE.md) for detailed documentation.

## Project Structure

```
├── app/
│   ├── Http/Controllers/Auth/  # Authentication controllers
│   └── Models/                  # Eloquent models
├── database/
│   ├── migrations/              # Database migrations
│   └── database.sqlite          # SQLite database file
├── resources/
│   └── views/
│       ├── auth/                # Authentication views
│       ├── components/          # Blade components
│       ├── layouts/             # Layout templates
│       ├── profile/             # Profile management views
│       └── dashboard.blade.php  # Dashboard view
└── routes/
    ├── web.php                  # Web routes
    └── auth.php                 # Authentication routes
```

## Customization

### Styling
The application uses Tailwind CSS. Customize styles in:
- `tailwind.config.js` - Tailwind configuration
- `resources/css/app.css` - Custom CSS

### Views
All Blade templates are in `resources/views/`:
- Modify authentication pages in `resources/views/auth/`
- Edit layouts in `resources/views/layouts/`
- Customize components in `resources/views/components/`

### Database
To modify the database schema:
1. Create a new migration: `php artisan make:migration create_table_name`
2. Edit the migration file in `database/migrations/`
3. Run migrations: `php artisan migrate`

## Testing

Run the test suite:

```bash
php artisan test
```

## Additional Commands

```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# List all routes
php artisan route:list

# Create a new controller
php artisan make:controller ControllerName

# Create a new model
php artisan make:model ModelName -m
```

## Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze Documentation](https://laravel.com/docs/starter-kits#laravel-breeze)
- [Blade Templates](https://laravel.com/docs/blade)
- [Tailwind CSS](https://tailwindcss.com/docs)

## License

This Laravel application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
