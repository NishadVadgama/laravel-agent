<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Enums\StreamEventType;

class OpenRouterDemoController extends Controller
{
    /**
     * Available providers and their models
     */
    private function getAvailableModels()
    {
        return [
            'OpenAI' => [
                'gpt-4o-mini' => 'GPT-4o Mini (Cheap)',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Cheap)',
            ],
            'OpenRouter' => [
                'qwen/qwen3-235b-a22b:free' => 'Qwen 235B (Free)',
                'meta-llama/llama-3.2-3b-instruct:free' => 'Llama 3.2 3B (Free)',
                'microsoft/phi-3-mini-128k-instruct:free' => 'Phi-3 Mini (Free)',
                'google/gemma-2-9b-it:free' => 'Gemma 2 9B (Free)',
            ],
        ];
    }

    /**
     * Show the settings page
     */
    public function settings()
    {
        $models = $this->getAvailableModels();
        $currentProvider = session('ai_provider', 'OpenRouter');
        $currentModel = session('ai_model', 'qwen/qwen3-235b-a22b:free');

        return view('openrouter.settings', compact('models', 'currentProvider', 'currentModel'));
    }

    /**
     * Save provider and model settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:OpenAI,OpenRouter',
            'model' => 'required|string',
        ]);

        session([
            'ai_provider' => $request->input('provider'),
            'ai_model' => $request->input('model'),
        ]);

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    /**
     * Show the text generation demo page
     */
    public function textDemo()
    {
        $provider = session('ai_provider', 'OpenRouter');
        $model = session('ai_model', 'qwen/qwen3-235b-a22b:free');
        
        return view('openrouter.text', compact('provider', 'model'));
    }

    /**
     * Show the streaming demo page
     */
    public function streamDemo()
    {
        $provider = session('ai_provider', 'OpenRouter');
        $model = session('ai_model', 'qwen/qwen3-235b-a22b:free');
        
        return view('openrouter.stream', compact('provider', 'model'));
    }

    /**
     * Generate text using selected provider
     */
    public function generateText(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        try {
            $provider = session('ai_provider', 'OpenRouter');
            $model = session('ai_model', 'qwen/qwen3-235b-a22b:free');

            $providerEnum = $provider === 'OpenAI' ? Provider::OpenAI : Provider::OpenRouter;

            $response = Prism::text()
                ->using($providerEnum, $model)
                ->withPrompt($request->input('prompt'))
                ->generate();

            return response()->json([
                'success' => true,
                'text' => $response->text,
                'usage' => $response->usage,
            ]);
        } catch (\Exception $e) {
            Log::error('Text generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream text generation using selected provider
     */
    public function generateStream(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        return response()->stream(function () use ($request) {
            try {
                $provider = session('ai_provider', 'OpenRouter');
                $model = session('ai_model', 'qwen/qwen3-235b-a22b:free');

                $providerEnum = $provider === 'OpenAI' ? Provider::OpenAI : Provider::OpenRouter;

                $stream = Prism::text()
                    ->using($providerEnum, $model)
                    ->withPrompt($request->input('prompt'))
                    ->asStream();

                foreach ($stream as $event) {
                    if ($event->type() === StreamEventType::TextDelta) {
                        echo "data: " . json_encode([
                            'type' => 'delta',
                            'text' => $event->delta,
                        ]) . "\n\n";
                        
                        ob_flush();
                        flush();
                    }
                }

                // Send completion event
                echo "data: " . json_encode([
                    'type' => 'done',
                ]) . "\n\n";
                
                ob_flush();
                flush();

            } catch (\Exception $e) {
                Log::error('Streaming error: ' . $e->getMessage());
                
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
