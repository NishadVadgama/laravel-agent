# OpenRouter Demo with Prism PHP

This demo showcases text generation and streaming capabilities using Prism PHP with OpenRouter provider.

## Model
- **Provider:** OpenRouter
- **Model:** `qwen/qwen3-235b-a22b:free`

## What Was Created

### 1. Controller
- **File:** `app/Http/Controllers/OpenRouterDemoController.php`
- **Methods:**
  - `textDemo()` - Shows the text generation page
  - `streamDemo()` - Shows the streaming page
  - `generateText()` - Handles text generation API requests
  - `generateStream()` - Handles streaming API requests with Server-Sent Events (SSE)

### 2. Views
- **Text Demo:** `resources/views/openrouter/text.blade.php`
  - Form to submit prompts
  - Displays complete responses with token usage
  - Uses fetch API for non-streaming requests

- **Stream Demo:** `resources/views/openrouter/stream.blade.php`
  - Form to submit prompts
  - Real-time streaming display using EventSource
  - Stop button to cancel streaming
  - Shows response as it's being generated

### 3. Routes
Added to `routes/web.php` (requires authentication):
- `GET /openrouter/text` - Text generation demo page
- `POST /openrouter/text/generate` - Generate text endpoint
- `GET /openrouter/stream` - Streaming demo page
- `GET /openrouter/stream/generate` - Stream text endpoint

### 4. Dashboard
Updated `resources/views/dashboard.blade.php` with links to both demos.

## How to Use

1. **Login to your application**
   - All demo routes require authentication

2. **Access the demos:**
   - Visit `/dashboard` to see links to both demos
   - Or go directly to:
     - `/openrouter/text` for text generation
     - `/openrouter/stream` for streaming

3. **Text Generation Demo:**
   - Enter a prompt in the textarea
   - Click "Generate Text"
   - Wait for the complete response
   - View token usage statistics

4. **Streaming Demo:**
   - Enter a prompt in the textarea
   - Click "Start Streaming"
   - Watch the response appear in real-time
   - Click "Stop" to cancel if needed

## Features

### Text Generation
- ✅ Non-streaming responses
- ✅ Token usage tracking
- ✅ Error handling
- ✅ Loading states
- ✅ Clean, modern UI with Tailwind CSS

### Streaming
- ✅ Real-time text streaming
- ✅ Server-Sent Events (SSE)
- ✅ Stop/cancel functionality
- ✅ Error handling
- ✅ Connection management

## Technical Details

### Text Generation Flow
1. User submits prompt via form
2. JavaScript sends POST request to `/openrouter/text/generate`
3. Controller calls Prism with OpenRouter provider
4. Complete response returned as JSON
5. UI displays text and usage stats

### Streaming Flow
1. User submits prompt via form
2. JavaScript creates EventSource connection to `/openrouter/stream/generate`
3. Controller initiates Prism streaming
4. Text deltas sent as SSE events (`data: {...}`)
5. UI appends each delta to display
6. Connection closed when complete

### Error Handling
- Network errors caught and displayed
- API errors logged and shown to user
- Stream errors terminate connection gracefully
- Validation errors prevent invalid requests

## Environment Requirements

Make sure your `.env` file has:
```env
OPENROUTER_API_KEY=your_api_key_here
OPENROUTER_URL=https://openrouter.ai/api/v1
OPENROUTER_SITE_HTTP_REFERER=https://your-site.example
OPENROUTER_SITE_X_TITLE="Your Site Name"
```

## Testing

Try these example prompts:
- "Write a short poem about artificial intelligence"
- "Explain quantum computing in simple terms"
- "Tell me a story about a robot learning to paint" (good for streaming)
- "What are the benefits of using PHP for web development?"

## No Additional Packages Needed

This implementation uses:
- **Prism PHP** (already installed)
- **Blade templating** (Laravel default)
- **Vanilla JavaScript** (no frontend framework)
- **Tailwind CSS** (Laravel Breeze default)
- **Server-Sent Events** (native browser API)

No additional npm packages or PHP libraries required!
