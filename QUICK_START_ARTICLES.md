# Quick Start Guide - Articles Feature

## Login Credentials

### Admin Account
```
Email: admin@example.com
Password: password
```
**Access**: Can view all articles from all users

### Regular User Account
```
Email: test@example.com
Password: password
```
**Access**: Can only view their own articles

### Additional Users
All seeded users have the password: `password`

## Quick Commands

### Reset and Reseed Database
```bash
cd /Users/nvadgama/Herd/llmdemo
php artisan migrate:fresh --seed
```

### Check Database Stats
```bash
php artisan tinker --execute="
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
echo 'Articles: ' . \App\Models\Article::count() . PHP_EOL;
echo 'Admins: ' . \App\Models\User::where('role', 'admin')->count() . PHP_EOL;
"
```

### List All Users
```bash
php artisan tinker --execute="
\App\Models\User::all()->each(function(\$user) {
    echo \$user->name . ' (' . \$user->email . ') - Role: ' . \$user->role . ' - Articles: ' . \$user->articles()->count() . PHP_EOL;
});
"
```

## Testing the Feature

### Test 1: Admin Can See Everything
1. Login as `admin@example.com` / `password`
2. Navigate to http://llmdemo.test/articles
3. ✅ You should see articles from all users
4. ✅ Each article should show the author's name
5. ✅ A blue banner should say "Admin View: You can see all articles from all users"

### Test 2: Users See Only Their Articles
1. Logout and login as `test@example.com` / `password`
2. Navigate to http://llmdemo.test/articles
3. ✅ You should see only 3 articles (all belonging to Test User)
4. ✅ No author names are shown
5. ✅ No admin banner is displayed

### Test 3: Unauthorized Access Prevention
1. While logged in as regular user (test@example.com)
2. Note an article ID that belongs to admin (from articles page when logged in as admin)
3. Try to access http://llmdemo.test/articles/{admin-article-id}
4. ✅ You should receive a "403 | Forbidden" error

### Test 4: Article Details Page
1. Login as any user
2. Click on any article from the list
3. ✅ See full article details
4. ✅ See article metadata (ID, slug, dates)
5. ✅ See author information
6. ✅ See status badge (Draft/Published)

## Sample Database Queries

### Get All Admin Users
```php
$admins = User::where('role', 'admin')->get();
```

### Get User's Articles
```php
$user = User::find(1);
$articles = $user->articles;
```

### Get Published Articles Only
```php
$published = Article::where('status', 'published')->get();
```

### Get Articles with User Info
```php
$articles = Article::with('user')->get();
```

### Count Articles by User
```php
User::withCount('articles')->get();
```

## Creating New Data

### Create a New User
```bash
php artisan tinker
```
```php
$user = User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'role' => 'user'
]);
```

### Create a New Admin
```php
$admin = User::factory()->admin()->create([
    'name' => 'Jane Admin',
    'email' => 'jane@example.com'
]);
```

### Create Articles for User
```php
use App\Models\Article;
use Illuminate\Support\Str;

$user = User::where('email', 'test@example.com')->first();

Article::create([
    'user_id' => $user->id,
    'title' => 'My Custom Article',
    'slug' => Str::slug('My Custom Article'),
    'description' => 'This is my custom article content.',
    'date' => now(),
    'status' => 'published'
]);
```

### Bulk Create Articles
```php
Article::factory(10)->create(['user_id' => 1]);
```

## Database Schema Quick Reference

### Users Table
- `id`, `name`, `email`, `role` (admin/user), `password`

### Articles Table
- `id`, `user_id`, `title`, `slug`, `description`, `date`, `status` (draft/published)

## Troubleshooting

### Can't See Articles Page
- Make sure you're logged in
- Check route exists: `php artisan route:list | grep articles`

### Articles Not Showing
- Verify data exists: `php artisan tinker --execute="echo Article::count();"`
- Re-seed: `php artisan db:seed`

### 403 Errors
- This is normal when trying to access other users' articles
- Login as admin to view all articles

### Navigation Link Not Showing
- Clear view cache: `php artisan view:clear`
- Rebuild assets: `npm run build`

## URLs

- **Homepage**: http://llmdemo.test
- **Login**: http://llmdemo.test/login
- **Dashboard**: http://llmdemo.test/dashboard
- **Articles**: http://llmdemo.test/articles
- **Profile**: http://llmdemo.test/profile

## Files Modified/Created

### New Files
- `app/Models/Article.php`
- `app/Http/Controllers/ArticleController.php`
- `database/migrations/2025_12_19_101926_add_role_to_users_table.php`
- `database/migrations/2025_12_19_101927_create_articles_table.php`
- `database/factories/ArticleFactory.php`
- `resources/views/articles/index.blade.php`
- `resources/views/articles/show.blade.php`
- `ARTICLES_FEATURE.md`

### Modified Files
- `app/Models/User.php` - Added role, articles relationship
- `database/factories/UserFactory.php` - Added role support
- `database/seeders/DatabaseSeeder.php` - Added comprehensive seeding
- `routes/web.php` - Added article routes
- `resources/views/layouts/navigation.blade.php` - Added Articles link
- `README.md` - Updated with new features

## Next Steps

Want to extend the feature? Consider adding:
- Create/Edit/Delete articles (CRUD)
- Rich text editor for descriptions
- Categories and tags
- Search and filtering
- Comments system
- Like/favorite functionality

See `ARTICLES_FEATURE.md` for more detailed documentation.
