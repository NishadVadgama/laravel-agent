# Articles Feature Documentation

## Overview

This Laravel application now includes a complete articles management system with user roles and permissions. The system allows users to create and manage articles, with role-based access control for viewing content.

## Database Schema

### Users Table (Extended)
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `role` - Enum ('admin', 'user') - User role
- `password` - Hashed password
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

### Articles Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Article title
- `slug` - Unique URL-friendly slug
- `description` - Article content/description
- `date` - Publication date
- `status` - Enum ('draft', 'published') - Article status
- `created_at`, `updated_at` - Timestamps

## User Roles

### Admin Users
- Can view **all articles** from all users
- Have full access to the system
- Can see author information for each article
- Visual indicator showing they're viewing as admin

### Regular Users
- Can only view **their own articles**
- Cannot see other users' articles
- Access is restricted at both controller and database level

## Features

### 1. User Management
- User factory with role support
- Admin and regular user creation
- Password: `password` for all seeded users

### 2. Article Management
- Each user can have multiple articles
- Articles have title, slug, description, date, and status
- Automatic slug generation with uniqueness
- Draft and published status support

### 3. Access Control
- Route-based middleware authentication
- Controller-level authorization checks
- Database queries filtered by user role
- 403 Forbidden response for unauthorized access

### 4. User Interface
- **Articles Index Page** (`/articles`)
  - Lists articles based on user role
  - Shows all articles for admins
  - Shows only user's own articles for regular users
  - Pagination support (15 articles per page)
  - Status badges (Draft/Published)
  - Author information for admin view
  
- **Article Detail Page** (`/articles/{id}`)
  - Full article content
  - Author information
  - Publication date
  - Status indicator
  - Article metadata (ID, slug, timestamps)
  - Access control (403 if unauthorized)

- **Navigation**
  - Added "Articles" link to main navigation
  - Active state indication
  - Responsive mobile menu support

## Dummy Data

The database seeder creates:

### Users
1. **Admin User**
   - Email: `admin@example.com`
   - Password: `password`
   - Role: `admin`
   - Articles: 5

2. **Test User**
   - Email: `test@example.com`
   - Password: `password`
   - Role: `user`
   - Articles: 3

3. **10 Additional Users**
   - Random names and emails
   - Role: `user`
   - Articles: 2-5 per user (random)

**Total**: 12 users, ~36 articles

### Articles
- Random titles using Faker
- Unique slugs with numbering
- 3 paragraphs of lorem ipsum text
- Random dates from the past year
- Random status (draft/published)

## Models & Relationships

### User Model (`app/Models/User.php`)
```php
// Relationships
public function articles() // hasMany
public function isAdmin()   // Helper method
```

### Article Model (`app/Models/Article.php`)
```php
// Relationships
public function user() // belongsTo

// Fillable fields
['user_id', 'title', 'slug', 'description', 'date', 'status']
```

## Routes

```php
// Protected by 'auth' middleware
GET  /articles           - List articles (ArticleController@index)
GET  /articles/{id}      - Show article (ArticleController@show)
```

## Testing the Feature

### 1. Login as Admin
```
Email: admin@example.com
Password: password
```
- Navigate to `/articles`
- You should see all articles from all users
- Each article shows the author's name

### 2. Login as Regular User
```
Email: test@example.com
Password: password
```
- Navigate to `/articles`
- You should only see 3 articles (your own)
- No author information is shown

### 3. Try Unauthorized Access
- Login as regular user
- Try to access an article belonging to another user
- You should receive a 403 Forbidden error

## Database Commands

### Run Migrations
```bash
php artisan migrate
```

### Seed Database
```bash
php artisan db:seed
```

### Reset & Seed (Fresh Start)
```bash
php artisan migrate:fresh --seed
```

### Create More Dummy Data
You can modify `database/seeders/DatabaseSeeder.php` to create more users or articles:

```php
// Create 50 additional users
$users = User::factory(50)->create();

// Create 10 articles per user
foreach ($users as $user) {
    Article::factory(10)->create(['user_id' => $user->id]);
}
```

## Factories

### UserFactory
- Creates users with random data
- `admin()` state for admin users
- Default password: `password`

### ArticleFactory
- Creates articles with random data
- Auto-generates unique slugs
- Random dates and status
- Can override `user_id` when creating

## API Examples

### Creating Articles Programmatically

```php
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

$user = User::find(1);

$article = Article::create([
    'user_id' => $user->id,
    'title' => 'My New Article',
    'slug' => Str::slug('My New Article'),
    'description' => 'Article content here...',
    'date' => now(),
    'status' => 'published',
]);
```

### Querying Articles

```php
// Get all articles for current user
$articles = auth()->user()->articles()->get();

// Get only published articles
$articles = auth()->user()->articles()
    ->where('status', 'published')
    ->get();

// Admin: Get all articles with user info
$articles = Article::with('user')->get();
```

## Security Features

1. **Authentication Required**: All article routes require login
2. **Authorization Checks**: Users cannot access other users' articles
3. **Database-Level Filtering**: Queries are filtered by user_id
4. **Role-Based Access**: Admin role can bypass user_id filtering
5. **Mass Assignment Protection**: Fillable fields explicitly defined
6. **SQL Injection Prevention**: Eloquent ORM parameterized queries

## Customization

### Adding More Fields to Articles
1. Create a migration:
   ```bash
   php artisan make:migration add_fields_to_articles_table
   ```

2. Add fields to migration and model's `$fillable` array

3. Update the factory and views

### Adding Article Categories
1. Create categories table and migration
2. Add `category_id` to articles table
3. Create Category model with relationship
4. Update factory to assign random categories

### Adding CRUD Operations
The current implementation is read-only. To add create/edit/delete:
1. Create form views
2. Add routes (POST, PUT, DELETE)
3. Add controller methods (create, store, edit, update, destroy)
4. Add authorization policies

## File Structure

```
app/
├── Http/Controllers/
│   └── ArticleController.php        # Article routes controller
├── Models/
    ├── Article.php                   # Article model
    └── User.php                      # User model (extended)

database/
├── factories/
│   ├── ArticleFactory.php           # Article factory
│   └── UserFactory.php              # User factory (extended)
├── migrations/
│   ├── 2025_12_19_101926_add_role_to_users_table.php
│   └── 2025_12_19_101927_create_articles_table.php
└── seeders/
    └── DatabaseSeeder.php           # Main seeder

resources/views/
├── articles/
│   ├── index.blade.php              # Articles list
│   └── show.blade.php               # Article detail
└── layouts/
    └── navigation.blade.php         # Updated navigation

routes/
└── web.php                          # Routes definition
```

## Troubleshooting

### No Articles Showing
- Make sure you've run migrations: `php artisan migrate`
- Make sure you've seeded the database: `php artisan db:seed`
- Check you're logged in
- Clear cache: `php artisan cache:clear`

### 403 Errors
- This is expected behavior when trying to access another user's article
- Make sure the article belongs to the logged-in user
- Login as admin to view all articles

### Pagination Not Working
- Make sure you have more than 15 articles
- Check that Tailwind CSS is compiled: `npm run build`

## Future Enhancements

Possible features to add:
- [ ] Create new articles (CRUD)
- [ ] Edit/delete articles
- [ ] Categories and tags
- [ ] Search functionality
- [ ] Rich text editor
- [ ] Image uploads
- [ ] Comments system
- [ ] Article sharing
- [ ] Export to PDF
- [ ] Article analytics

---

**Note**: All seeded users have the password `password` for easy testing.
