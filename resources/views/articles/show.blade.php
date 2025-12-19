<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Article Details') }}
            </h2>
            <a href="{{ route('articles.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                ← Back to Articles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($article->status) }}
                            </span>
                        </div>
                        
                        @if(auth()->user()->isAdmin() || $article->user_id === auth()->id())
                            <div class="flex gap-2">
                                <a href="{{ route('articles.edit', $article) }}" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('articles.destroy', $article) }}" 
                                    onsubmit="return confirm('Are you sure you want to delete this article? This action cannot be undone.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ $article->title }}
                    </h1>

                    <div class="flex items-center text-sm text-gray-600 mb-6 space-x-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ $article->user->name }}</span>
                            @if($article->user->isAdmin())
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">Admin</span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $article->date->format('F d, Y') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            {!! nl2br(e($article->description)) !!}
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-8 pt-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Article Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500">Article ID</dt>
                                    <dd class="text-gray-900">#{{ $article->id }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Slug</dt>
                                    <dd class="text-gray-900">{{ $article->slug }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Created</dt>
                                    <dd class="text-gray-900">{{ $article->created_at->format('M d, Y h:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-gray-900">{{ $article->updated_at->format('M d, Y h:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
