<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OpenRouterDemoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Articles Routes
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    
    // AI Settings
    Route::get('/ai/settings', [OpenRouterDemoController::class, 'settings'])->name('ai.settings');
    Route::post('/ai/settings', [OpenRouterDemoController::class, 'saveSettings'])->name('ai.settings.save');
    
    // OpenRouter Demo Routes
    Route::get('/openrouter/text', [OpenRouterDemoController::class, 'textDemo'])->name('openrouter.text');
    Route::post('/openrouter/text/generate', [OpenRouterDemoController::class, 'generateText'])->name('openrouter.text.generate');
    Route::get('/openrouter/stream', [OpenRouterDemoController::class, 'streamDemo'])->name('openrouter.stream');
    Route::get('/openrouter/stream/generate', [OpenRouterDemoController::class, 'generateStream'])->name('openrouter.stream.generate');
    
    // Agent Routes
    Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
    Route::post('/agent/chat', [AgentController::class, 'chat'])->name('agent.chat');
});

require __DIR__.'/auth.php';
