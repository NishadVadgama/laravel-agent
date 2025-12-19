<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the user's articles.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Admin can see all articles, regular users can only see their own
        if ($user->isAdmin()) {
            $articles = Article::with('user')
                ->latest('date')
                ->paginate(15);
        } else {
            $articles = Article::where('user_id', $user->id)
                ->latest('date')
                ->paginate(15);
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();
        
        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        Article::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'date' => $validated['date'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Article created successfully!');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        $user = Auth::user();
        
        // Check if user has permission to view this article
        if (!$user->isAdmin() && $article->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this article.');
        }

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        $user = Auth::user();
        
        // Check if user has permission to edit this article
        if (!$user->isAdmin() && $article->user_id !== $user->id) {
            abort(403, 'Unauthorized access to edit this article.');
        }

        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(StoreArticleRequest $request, Article $article)
    {
        $user = Auth::user();
        
        // Check if user has permission to update this article
        if (!$user->isAdmin() && $article->user_id !== $user->id) {
            abort(403, 'Unauthorized access to update this article.');
        }

        $validated = $request->validated();
        
        // Update slug if title changed
        if ($validated['title'] !== $article->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $validated['slug'] = $slug;
        }
        
        $article->update($validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Article updated successfully!');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article)
    {
        $user = Auth::user();
        
        // Check if user has permission to delete this article
        if (!$user->isAdmin() && $article->user_id !== $user->id) {
            abort(403, 'Unauthorized access to delete this article.');
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article deleted successfully!');
    }
}
