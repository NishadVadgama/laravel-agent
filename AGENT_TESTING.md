# AI Agent Testing Guide

## Overview
This document provides testing instructions for the AI Agent feature with tool support.

## What We Built

### 1. Agent Controller (`app/Http/Controllers/AgentController.php`)
- **Purpose**: Manages the AI agent with tool functionality
- **Key Features**:
  - Article search tool that queries the database
  - Streaming chat responses
  - Multi-step agent execution (up to 5 steps)
  - Tool call notifications in real-time

### 2. Article Search Tool
The agent has access to a `search_articles` tool that can:
- Search articles by title or description
- Filter by status (published, draft, or all)
- Return up to 10 results
- Display article details including title, status, date, author, and description

**Tool Parameters**:
- `query` (string): Search term for titles and descriptions (optional)
- `status` (string): Filter by "published", "draft", or "all" (default: "all")

### 3. Chat UI (`resources/views/agent/chat.blade.php`)
- Modern, gradient-based design with purple/indigo theme
- Real-time streaming responses
- Tool usage indicators with animated badges
- Typing indicators
- Status updates
- Conversation history tracking
- Auto-scroll to latest messages

## Testing Instructions

### Step 1: Access the Agent
1. Log in to the application
2. Click on "AI Agent" in the navigation menu, or
3. Go to Dashboard and click "🤖 AI Agent with Tools →"

### Step 2: Test Basic Queries

Try these sample queries to test the tool functionality:

#### Query 1: Get All Published Articles
```
Show me all published articles
```
**Expected**: Agent will use the `search_articles` tool with status="published"

#### Query 2: Search by Keyword
```
Find articles about Laravel
```
**Expected**: Agent will search for "Laravel" in titles and descriptions

#### Query 3: Get Draft Articles
```
What draft articles do we have?
```
**Expected**: Agent will use the tool with status="draft"

#### Query 4: Specific Search
```
Are there any articles about PHP?
```
**Expected**: Agent will search for "PHP" in the database

#### Query 5: Follow-up Question
```
Tell me more about the first one
```
**Expected**: Agent will use context from previous response

### Step 3: Verify Tool Usage

When the agent uses a tool, you should see:
1. **Tool Badge**: Animated purple badge showing "🔧 Using tool: search_articles"
2. **Status Update**: Bottom status changes to "Using tool: search_articles"
3. **Tool Completion**: Status updates to "Tool completed: search_articles"
4. **Response**: Agent provides formatted results from the database

### Step 4: Check Streaming

Verify that:
- Responses appear word by word (streaming)
- Tool calls happen in real-time
- No page refresh needed
- Smooth animations and transitions

### Step 5: Test Error Handling

Try edge cases:
1. Very long queries (close to 2000 character limit)
2. Queries about non-existent articles
3. Multiple rapid messages

## Technical Details

### Database Schema Used
The tool queries the `articles` table:
- `id`: Primary key
- `user_id`: Foreign key to users
- `title`: Article title
- `slug`: URL-friendly slug
- `description`: Article content/description
- `date`: Publication date
- `status`: Either "published" or "draft"
- `created_at`, `updated_at`: Timestamps

### Provider Configuration
The agent uses:
- Provider: From session (OpenRouter or OpenAI)
- Model: From session (configured in AI Settings)
- Max Steps: 5 (allows multiple tool calls)
- System Prompt: Instructs agent on tool usage

### Streaming Architecture
1. Client makes GET request to `/agent/chat`
2. Server opens EventSource stream
3. Events sent:
   - `delta`: Text chunks
   - `tool_call`: Tool usage notification
   - `tool_result`: Tool completion notification
   - `done`: Stream completion
   - `error`: Error messages

## What to Look For

### Success Indicators
✅ Agent responds conversationally
✅ Tool is called when appropriate
✅ Real database results are returned
✅ Formatting is clean and readable
✅ Status indicators update correctly
✅ No console errors
✅ Smooth streaming experience

### Potential Issues
❌ Tool not being called (check provider/model compatibility)
❌ Database connection errors (check .env configuration)
❌ Streaming not working (check browser EventSource support)
❌ Timeout errors (check server configuration)

## Sample Conversation Flow

```
User: "Show me all published articles"
Agent: 🔧 Using tool: search_articles
Agent: "I found [X] published articles. Here they are:
       
       **Article Title 1**
       Status: published
       Date: 2025-12-19
       Author: John Doe
       Description: Lorem ipsum...
       ---
       
       **Article Title 2**
       ..."

User: "Are there any about Laravel?"
Agent: 🔧 Using tool: search_articles
Agent: "Yes! I found [Y] articles about Laravel:
       ..."
```

## Next Steps

After verifying basic functionality:
1. Add more tools (e.g., create article, update article)
2. Implement structured output for specific queries
3. Add more sophisticated search capabilities
4. Integrate with other database tables
5. Add authentication/authorization checks for sensitive operations

## Configuration

Current configuration in `AgentController`:
- Max steps: 5
- Streaming: Enabled
- Tool error handling: Enabled (default)
- Message history: Full conversation context
- System prompt: Guides agent behavior

## Troubleshooting

### Tool Not Being Called
- Check if provider supports tool calling
- Verify model is compatible with tools
- Check max_steps is set (minimum 2)

### No Results from Database
- Verify articles exist: `php artisan tinker --execute="App\Models\Article::count()"`
- Check database connection
- Review search query syntax

### Streaming Issues
- Clear browser cache
- Check for ad blockers
- Verify EventSource support
- Check server timeout settings

## Database Stats
Current database state:
- Articles: 37
- Users: 13

You can verify at any time with:
```bash
php artisan tinker --execute="echo 'Articles: ' . App\Models\Article::count(); echo PHP_EOL; echo 'Users: ' . App\Models\User::count();"
```
