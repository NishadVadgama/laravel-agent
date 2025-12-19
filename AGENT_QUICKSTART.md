# AI Agent Quick Start Guide

## What This Does

You now have a **fully functional AI Agent** that can search and retrieve articles from your Laravel database using Prism PHP's tool-calling capabilities.

## Access the Agent

1. **Login to your app**
2. **Click "AI Agent"** in the navigation menu (or go to `/agent`)
3. **Start chatting!**

## Try These Examples

### Example 1: Get All Published Articles
**Ask:** 
> Show me all published articles

**What Happens:**
- 🔧 Agent calls `search_articles` tool with `status="published"`
- 📊 Tool queries database: `Article::where('status', 'published')`
- ✅ Returns 11 published articles with details

---

### Example 2: Search by Keyword
**Ask:**
> Find articles about Laravel

**What Happens:**
- 🔧 Agent calls `search_articles` tool with `query="Laravel"`
- 📊 Tool searches: `WHERE title LIKE '%Laravel%' OR description LIKE '%Laravel%'`
- ✅ Returns matching articles

---

### Example 3: Get Drafts
**Ask:**
> What draft articles do we have?

**What Happens:**
- 🔧 Agent calls `search_articles` tool with `status="draft"`
- 📊 Tool queries: `Article::where('status', 'draft')`
- ✅ Returns 26 draft articles (currently in your DB)

---

### Example 4: Follow-up Questions
**Ask:**
> Tell me more about the first one

**What Happens:**
- 💬 Agent uses conversation history
- 🧠 Refers to previous results
- ✅ Provides detailed information about specific article

---

## What You'll See

### 1. **Tool Usage Indicator**
When the agent uses a tool, you'll see:
```
🔧 Using tool: search_articles
```
Appears as an animated purple badge

### 2. **Real-time Streaming**
- Responses appear word by word
- Smooth typing animation
- No page refresh needed

### 3. **Status Updates**
Bottom of the screen shows:
- 🟢 "Ready" - Waiting for input
- 🟡 "Processing..." - Thinking
- 🔵 "Using tool: search_articles" - Calling tool
- 🟢 "Tool completed" - Done

### 4. **Formatted Results**
Agent returns results in readable format:
```
**Article Title**
Status: published
Date: 2025-12-19
Author: Admin User
Description: Article content here...
---
```

## Current Database Stats

Your database has:
- **37 total articles**
- **11 published articles**
- **26 draft articles**
- **13 users**

All of this data is accessible to the agent!

## How It Works Behind the Scenes

```mermaid
User Message
    ↓
Agent Analyzes Query
    ↓
Decides to Use Tool: search_articles
    ↓
Tool Queries Laravel Database
    ↓
Returns Results to Agent
    ↓
Agent Formats Response
    ↓
Streams to Your Screen
```

## The Tool: `search_articles`

### What It Does
Searches your `articles` table using Laravel Eloquent

### Parameters
- `query` (optional): Search term for title/description
- `status` (optional): "published", "draft", or "all"

### What It Returns
- Article title
- Status (published/draft)
- Publication date
- Author name
- Description (first 150 chars)
- Up to 10 results

### Example Tool Call
```php
search_articles(
    query: "Laravel",
    status: "published"
)
```

Executes:
```php
Article::with('user')
    ->where('status', 'published')
    ->where(function($q) {
        $q->where('title', 'like', '%Laravel%')
          ->orWhere('description', 'like', '%Laravel%');
    })
    ->limit(10)
    ->get();
```

## Natural Language Examples

The agent understands various ways of asking:

### Asking About Published Articles
- "Show me published articles"
- "What articles are published?"
- "List all published content"
- "Which articles are live?"

### Searching Content
- "Find articles about PHP"
- "Search for Laravel content"
- "Are there any articles about databases?"
- "Look for articles containing 'tutorial'"

### Asking About Drafts
- "Show me drafts"
- "What articles are in draft?"
- "List unpublished articles"
- "Which articles aren't published yet?"

### General Queries
- "How many articles do we have?"
- "Show me recent articles"
- "What content is available?"
- "List all articles"

## Tips for Best Results

### ✅ Do This
- Ask clear, specific questions
- Mention "published" or "draft" if you want to filter
- Include keywords when searching
- Ask follow-up questions for more details

### ❌ Avoid This
- Very vague questions like "tell me something"
- Questions unrelated to articles (tool won't be called)
- Extremely long queries (2000+ characters)

## Conversation Example

```
You: Show me all published articles

Agent: 🔧 Using tool: search_articles

Agent: I found 11 published articles. Here they are:

**Aperiam provident placeat iste aut ut sint.**
Status: published
Date: 2025-12-19
Author: Admin User
Description: [Article content...]
---

**Quis consequatur reiciendis voluptatem sint.**
Status: published
Date: 2025-12-19
Author: Admin User
Description: [Article content...]
---

[... more articles ...]

You: How many are drafts?

Agent: 🔧 Using tool: search_articles

Agent: There are 26 draft articles in the database. Would you like me to show you some of them?

You: Yes, show me 5

Agent: 🔧 Using tool: search_articles

Agent: Here are 5 draft articles:
[... results ...]
```

## Technical Details

### Provider & Model
Uses your configured settings from `/ai/settings`:
- **Provider**: OpenRouter or OpenAI
- **Model**: Your selected model

### Max Steps
Set to **5 steps**, allowing:
- Multiple tool calls in one conversation
- Complex multi-step reasoning
- Follow-up queries

### Streaming
Uses **Server-Sent Events (SSE)** for real-time updates

### Error Handling
- Database errors are caught and reported gracefully
- Tool failures don't break the conversation
- Network issues are handled with reconnection

## Next Steps to Try

1. **Ask about published articles** - See the tool in action
2. **Search for specific content** - Test the search functionality
3. **Compare drafts vs published** - See filtering work
4. **Ask follow-up questions** - Test conversation memory

## Extending the Agent

Want to add more capabilities? You can easily add new tools:

- **Create Article Tool**: Let agent create articles
- **Update Article Tool**: Modify existing articles
- **User Search Tool**: Find user information
- **Analytics Tool**: Get statistics and insights

Check `app/Http/Controllers/AgentController.php` to see how tools are defined!

## Files to Explore

- **Controller**: `app/Http/Controllers/AgentController.php`
- **View**: `resources/views/agent/chat.blade.php`
- **Routes**: `routes/web.php`
- **Testing Guide**: `AGENT_TESTING.md`
- **Implementation**: `AGENT_IMPLEMENTATION.md`

---

## 🎉 You're Ready!

Go to `/agent` and start chatting with your AI agent. It's connected to your real database and ready to help you explore your articles!
