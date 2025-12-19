# AI Agent Troubleshooting Guide

## Common Issues and Solutions

### Issue 1: "Connection error. Please try again."

This error appears when the EventSource connection to the backend fails.

#### Possible Causes:

1. **Provider/Model Not Configured**
   - **Solution**: Go to `/ai/settings` and select a provider and model
   - The agent now uses OpenRouter with a free model as default if not configured

2. **API Key Missing or Invalid**
   - **Check**: Verify `.env` file has valid API keys
   ```bash
   # For OpenAI
   OPENAI_API_KEY=sk-...
   
   # For OpenRouter
   OPENROUTER_API_KEY=sk-or-v1-...
   ```
   - **Solution**: Add/update API keys and restart server

3. **Provider Rate Limit**
   - **Check logs**: `tail -f storage/logs/laravel.log`
   - **Solution**: Wait a few minutes or switch providers in settings

4. **Authentication Error (401)**
   - Error: "Missing bearer or basic authentication in header"
   - **Solution**: API key is missing or invalid
   - Check: `php artisan tinker --execute="echo env('OPENAI_API_KEY');"`

### Issue 2: Session Not Persisting

If the provider/model settings aren't being saved:

```bash
# Clear session cache
php artisan cache:clear
php artisan config:clear

# Check session driver in .env
SESSION_DRIVER=file  # or database, redis, etc.
```

### Issue 3: Tool Not Being Called

If the agent responds but never uses the `search_articles` tool:

1. **Check Max Steps**: Must be at least 2
   - ✅ Already set to 5 in the code

2. **Model Doesn't Support Tools**
   - Some models don't support function calling
   - Try OpenAI gpt-4o-mini or gpt-3.5-turbo
   - Or OpenRouter with compatible models

3. **Query Not Clear Enough**
   - Try more explicit queries:
   - ✅ "Search for published articles"
   - ❌ "Tell me something"

### Issue 4: Database Connection Error

If you see database-related errors:

```bash
# Check database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check articles exist
php artisan tinker --execute="echo App\Models\Article::count();"
```

### Issue 5: Streaming Not Working

If responses don't stream (appear all at once):

1. **Browser Compatibility**
   - EventSource API required (all modern browsers)
   - Check console for JavaScript errors

2. **Server Configuration**
   - Some servers buffer output
   - Check: X-Accel-Buffering is set to 'no'
   - Try different server (Herd, Valet, php artisan serve)

3. **Timeout Issues**
   - Increase PHP timeout in php.ini
   - Or set in code: `set_time_limit(120);`

## Debugging Steps

### Step 1: Check API Keys

```bash
cd /Users/nvadgama/Herd/llmdemo

php artisan tinker --execute="
echo 'OpenAI API Key: ' . (env('OPENAI_API_KEY') ? 'SET' : 'NOT SET') . PHP_EOL;
echo 'OpenRouter API Key: ' . (env('OPENROUTER_API_KEY') ? 'SET' : 'NOT SET') . PHP_EOL;
"
```

### Step 2: Check Session

```bash
php artisan tinker --execute="
echo 'Provider: ' . session('ai_provider', 'NOT SET') . PHP_EOL;
echo 'Model: ' . session('ai_model', 'NOT SET') . PHP_EOL;
"
```

### Step 3: Check Database

```bash
php artisan tinker --execute="
echo 'Articles: ' . App\Models\Article::count() . PHP_EOL;
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
"
```

### Step 4: Test Tool Directly

```bash
php artisan tinker --execute="
\$articles = App\Models\Article::with('user:id,name')
    ->where('status', 'published')
    ->limit(3)
    ->get();
    
foreach (\$articles as \$article) {
    echo \$article->title . PHP_EOL;
}
"
```

### Step 5: Check Logs

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Or check recent errors
tail -n 100 storage/logs/laravel.log | grep ERROR
```

### Step 6: Test with cURL

```bash
# Test the endpoint directly
curl -X GET "http://llmdemo.test/agent/chat?message=Hello" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE" \
  -v
```

## Quick Fixes

### Fix 1: Reset Everything

```bash
cd /Users/nvadgama/Herd/llmdemo

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart if using artisan serve
# (Herd doesn't need restart)
```

### Fix 2: Set Default Provider

If session isn't working, you can set defaults in the controller or manually:

```bash
php artisan tinker --execute="
session(['ai_provider' => 'OpenRouter']);
session(['ai_model' => 'qwen/qwen3-235b-a22b:free']);
echo 'Session set!' . PHP_EOL;
"
```

### Fix 3: Use OpenAI Model (Default)

The app now defaults to OpenAI with gpt-4o-mini if settings aren't configured:
- Provider: `OpenAI`
- Model: `gpt-4o-mini`

This requires the `OPENAI_API_KEY` in `.env`.

Alternative: Use Free OpenRouter Model
- Provider: `OpenRouter`
- Model: `qwen/qwen3-235b-a22b:free`
- Requires: `OPENROUTER_API_KEY` in `.env`

### Fix 4: Test Without Tool

To verify the basic chat works, temporarily comment out the tool:

```php
// In AgentController.php chat() method
// Comment out:
// ->withTools([$articleSearchTool])
```

## Error Messages Explained

### "You hit a provider rate limit"
- **Meaning**: Too many requests to the AI provider
- **Solution**: Wait 1-5 minutes, or switch providers

### "Missing bearer or basic authentication in header"
- **Meaning**: API key not found or invalid
- **Solution**: Check `.env` file has correct key

### "Sending to model (gpt-4o-mini) failed: HTTP request returned status code 401"
- **Meaning**: Using OpenAI but API key is wrong
- **Solution**: Verify `OPENAI_API_KEY` in `.env` or switch to OpenRouter

### "Connection error. Please try again."
- **Meaning**: Generic JavaScript error, backend not responding
- **Solution**: Check Laravel logs for detailed error

## Verification Checklist

Before reporting an issue, verify:

- ✅ `.env` file has API keys
- ✅ Database has articles (`Article::count() > 0`)
- ✅ Session is working (`session('ai_provider')` returns value)
- ✅ Routes are registered (`php artisan route:list --path=agent`)
- ✅ No syntax errors (`php -l app/Http/Controllers/AgentController.php`)
- ✅ Laravel logs show no errors
- ✅ Browser console shows no errors
- ✅ Provider/Model configured in `/ai/settings`

## Still Having Issues?

1. **Check Laravel Version**
   ```bash
   php artisan --version
   ```

2. **Check Prism Version**
   ```bash
   composer show | grep prism
   ```

3. **Check PHP Version**
   ```bash
   php -v
   ```

4. **Review Full Error**
   - Check `storage/logs/laravel.log`
   - Check browser console (F12)
   - Look for stack traces

## Working Configuration Example

Here's a known working configuration:

### .env
```ini
# Default: OpenAI
OPENAI_API_KEY=sk-xxxxxxxxxxxxx
OPENAI_URL=https://api.openai.com/v1

# Alternative: OpenRouter (free models available)
OPENROUTER_API_KEY=sk-or-v1-xxxxxxxxxxxxx
OPENROUTER_URL=https://openrouter.ai/api/v1
```

### Session Settings (set via /ai/settings page)
- Provider: `OpenAI` (default)
- Model: `gpt-4o-mini` (default)

Or use OpenRouter:
- Provider: `OpenRouter`
- Model: `qwen/qwen3-235b-a22b:free` (free model)

### Database
- At least 1 article in the database
- Articles table has correct schema

## Getting Help

If you still have issues:
1. Check the error in `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify all checklist items above
4. Review `AGENT_IMPLEMENTATION.md` for architecture details
