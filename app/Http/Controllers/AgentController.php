<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Facades\Tool;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Enums\StreamEventType;

class AgentController extends Controller
{
    /**
     * Show the agent chat interface
     */
    public function index()
    {
        $provider = session('ai_provider', 'OpenAI');
        $model = session('ai_model', 'gpt-4o-mini');
        
        return view('agent.chat', compact('provider', 'model'));
    }

    /**
     * Create the article search tool
     */
    private function createArticleSearchTool()
    {
        return Tool::as('search_articles')
            ->for('Search for articles in the database by title, description, or status. Returns a list of matching articles with their details.')
            ->withStringParameter('query', 'The search term to look for in article titles and descriptions (optional, leave empty to get all articles)')
            ->withStringParameter('status', 'Filter by article status: "published", "draft", or "all" (default: "all")')
            ->using(function (string $query = '', string $status = 'all'): string {
                try {
                    // Build the query
                    $articlesQuery = Article::with('user:id,name');

                    // Filter by status if specified
                    if ($status !== 'all' && in_array($status, ['published', 'draft'])) {
                        $articlesQuery->where('status', $status);
                    }

                    // Search in title and description if query is provided
                    if (!empty($query)) {
                        $articlesQuery->where(function ($q) use ($query) {
                            $q->where('title', 'like', "%{$query}%")
                              ->orWhere('description', 'like', "%{$query}%");
                        });
                    }

                    // Get the articles
                    $articles = $articlesQuery->orderBy('date', 'desc')
                        ->limit(4)
                        ->get();

                    if ($articles->isEmpty()) {
                        return "No articles found matching your criteria.";
                    }

                    // Format the results
                    $result = "Found " . $articles->count() . " article(s):\n\n";
                    
                    foreach ($articles as $article) {
                        $result .= "**{$article->title}**\n";
                        $result .= "Status: {$article->status}\n";
                        $result .= "Date: {$article->date->format('Y-m-d')}\n";
                        $result .= "Author: {$article->user->name}\n";
                        $result .= "Description: " . substr($article->description, 0, 150) . (strlen($article->description) > 150 ? '...' : '') . "\n";
                        $result .= "---\n\n";
                    }

                    return $result;

                } catch (\Exception $e) {
                    Log::error('Article search tool error: ' . $e->getMessage());
                    return "Error searching articles: " . $e->getMessage();
                }
            });
    }

    /**
     * Chat with the agent (streaming response)
     */
    public function chat(Request $request)
    {
        // Debug logging
        Log::info('Agent chat request received', [
            'user_id' => auth()->id(),
            'has_user' => auth()->check(),
            'message' => $request->input('message'),
        ]);
        
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
        ]);

        return response()->stream(function () use ($request) {
            // Send immediate test to see if stream is working
            echo "data: " . json_encode(['type' => 'test', 'message' => 'Stream started']) . "\n\n";
            ob_flush();
            flush();
            
            try {
                Log::info('Starting agent stream');
                
                // Get provider and model from session, with fallbacks
                $provider = session('ai_provider');
                $model = session('ai_model');
                
                // If not set in session, use OpenAI with gpt-4o-mini as default
                if (!$provider || !$model) {
                    $provider = 'OpenAI';
                    $model = 'gpt-4o-mini';
                    
                    // Save to session for future use
                    session([
                        'ai_provider' => $provider,
                        'ai_model' => $model,
                    ]);
                }

                Log::info('Agent configuration', ['provider' => $provider, 'model' => $model]);

                $providerEnum = $provider === 'OpenAI' ? Provider::OpenAI : Provider::OpenRouter;

                // Create the article search tool
                $articleSearchTool = $this->createArticleSearchTool();
                
                Log::info('Tool created');

                // Build messages from history
                $history = array_slice($request->input('history', []), -3);
                $messages = [];
                
                foreach ($history as $msg) {
                    $messages[] = new \Prism\Prism\ValueObjects\Messages\UserMessage($msg['content']);
                }
                
                // Add current message
                $messages[] = new \Prism\Prism\ValueObjects\Messages\UserMessage($request->input('message'));
                
                Log::info('Creating Prism stream');

                // Create the agent with tools
                $stream = Prism::text()
                    ->using($providerEnum, $model)
                    ->withSystemPrompt('You are a helpful AI assistant that can search and retrieve articles from a database. When users ask about articles, use the search_articles tool to find relevant information. Be conversational and helpful.')
                    ->withMessages($messages)
                    ->withTools([$articleSearchTool])
                    ->withMaxSteps(5) // Allow multiple steps for tool usage
                    ->asStream();

                Log::info('Stream created, starting to iterate');
                
                $fullResponse = '';

                foreach ($stream as $event) {
                    Log::info('Stream event', ['type' => $event->type()->name]);
                    
                    if ($event->type() === StreamEventType::TextDelta) {
                        $fullResponse .= $event->delta;
                        
                        echo "data: " . json_encode([
                            'type' => 'delta',
                            'text' => $event->delta,
                        ]) . "\n\n";
                        
                        ob_flush();
                        flush();
                    } elseif ($event->type() === StreamEventType::ToolCall) {
                        // Send tool call notification
                        echo "data: " . json_encode([
                            'type' => 'tool_call',
                            'tool' => $event->toolCall->name,
                            'arguments' => $event->toolCall->arguments(),
                        ]) . "\n\n";
                        
                        ob_flush();
                        flush();
                    } elseif ($event->type() === StreamEventType::ToolResult) {
                        // Send tool result notification
                        echo "data: " . json_encode([
                            'type' => 'tool_result',
                            'tool' => $event->toolResult->toolName,
                            'result' => substr($event->toolResult->result, 0, 200), // Truncate for display
                        ]) . "\n\n";
                        
                        ob_flush();
                        flush();
                    }
                }

                // Send completion event
                echo "data: " . json_encode([
                    'type' => 'done',
                    'full_response' => $fullResponse,
                ]) . "\n\n";
                
                ob_flush();
                flush();

            } catch (\Exception $e) {
                Log::error('Agent chat error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                echo "data: " . json_encode([
                    'type' => 'error',
                    'error' => $e->getMessage(),
                ]) . "\n\n";
                
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
