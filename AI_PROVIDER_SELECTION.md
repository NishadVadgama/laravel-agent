# AI Provider Selection Feature

## Overview

The demo now supports selecting between different AI providers and models through a user-friendly settings page.

## Features

### Supported Providers

1. **OpenAI** (Cheap Models)
   - `gpt-4o-mini` - Latest mini model, great performance, very affordable
   - `gpt-3.5-turbo` - Fast and efficient, good for most tasks

2. **OpenRouter** (Free Models)
   - `qwen/qwen3-235b-a22b:free` - Large model with excellent performance
   - `meta-llama/llama-3.2-3b-instruct:free` - Meta's efficient small model
   - `microsoft/phi-3-mini-128k-instruct:free` - Microsoft's compact but powerful model
   - `google/gemma-2-9b-it:free` - Google's open model

## New Routes

- **GET `/ai/settings`** - Settings page to select provider and model
- **POST `/ai/settings`** - Save selected provider and model to session

## How It Works

1. **Select Provider & Model**: Visit `/ai/settings` to choose your preferred AI provider and model
2. **Settings Saved in Session**: Selected options are stored in Laravel session
3. **Used Across Demos**: Both text generation and streaming demos use the selected provider/model
4. **Easy Switching**: Change models anytime via the "Change Model" link on demo pages

## Usage Flow

```
Dashboard → Settings → Select Provider/Model → Save → Try Demos
```

Or directly from any demo page:

```
Demo Page → "Change Model" link → Settings → Select New Model → Save → Back to Demo
```

## Navigation

### From Dashboard:
- ⚙️ Configure AI Settings (highlighted)
- Text Generation Demo
- Streaming Demo

### From Demo Pages:
- "Change Model →" link in the header
- Links to other demos at the bottom

## Technical Details

### Session Storage
Settings are stored using Laravel's session:
```php
session([
    'ai_provider' => 'OpenAI',
    'ai_model' => 'gpt-4o-mini',
]);
```

### Controller Logic
The controller:
1. Retrieves selected provider/model from session
2. Maps provider name to Prism's `Provider` enum
3. Passes both to Prism's `using()` method

### Dynamic Model Dropdown
JavaScript dynamically shows/hides model options based on selected provider.

## Default Settings

- **Default Provider**: OpenRouter
- **Default Model**: qwen/qwen3-235b-a22b:free

## Environment Requirements

Make sure your `.env` has API keys configured:

```env
# OpenAI
OPENAI_API_KEY=your_openai_key

# OpenRouter
OPENROUTER_API_KEY=your_openrouter_key
OPENROUTER_URL=https://openrouter.ai/api/v1
OPENROUTER_SITE_HTTP_REFERER=https://your-site.example
OPENROUTER_SITE_X_TITLE="Your Site Name"
```

## Files Modified

1. **Controller**: `app/Http/Controllers/OpenRouterDemoController.php`
   - Added `settings()`, `saveSettings()`, `getAvailableModels()`
   - Updated all methods to use session-stored settings

2. **Views**:
   - Created: `resources/views/openrouter/settings.blade.php`
   - Updated: `text.blade.php`, `stream.blade.php`, `dashboard.blade.php`

3. **Routes**: `routes/web.php`
   - Added settings routes

## Styling

All buttons and forms use inline styling for consistent visibility across different environments.
