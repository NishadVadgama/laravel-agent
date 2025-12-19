<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Text Generation Demo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">
                                Using: <span class="font-semibold text-indigo-600">{{ $provider }}</span> - <span class="font-semibold text-indigo-600">{{ $model }}</span>
                            </p>
                        </div>
                        <a href="{{ route('ai.settings') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                            Change Model →
                        </a>
                    </div>

                    <form id="textForm" class="space-y-4">
                        @csrf
                        <div>
                            <label for="prompt" class="block text-sm font-medium text-gray-700 mb-2">
                                Enter your prompt:
                            </label>
                            <textarea 
                                id="prompt" 
                                name="prompt" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g., Write a short story about AI..."
                                required
                            ></textarea>
                        </div>

                        <div>
                            <button 
                                type="submit" 
                                id="generateBtn"
                                style="background-color: #4F46E5; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#4338CA'"
                                onmouseout="this.style.backgroundColor='#4F46E5'"
                            >
                                Generate Text
                            </button>
                        </div>
                    </form>

                    <div id="loading" class="hidden mt-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating response...
                        </div>
                    </div>

                    <div id="error" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-600"></p>
                    </div>

                    <div id="response" class="hidden mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Response:</h3>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div id="responseText" class="text-gray-800 whitespace-pre-wrap"></div>
                        </div>
                        <div id="usage" class="mt-3 text-sm text-gray-600"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Other Options:</h3>
                    <div class="space-y-2">
                        <a href="{{ route('ai.settings') }}" class="text-indigo-600 hover:text-indigo-800 underline block">
                            ⚙️ Change Provider/Model Settings
                        </a>
                        <a href="{{ route('openrouter.stream') }}" class="text-indigo-600 hover:text-indigo-800 underline block">
                            Try the Streaming Demo →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('textForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const prompt = document.getElementById('prompt').value;
            const generateBtn = document.getElementById('generateBtn');
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const response = document.getElementById('response');
            
            // Reset UI
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            response.classList.add('hidden');
            generateBtn.disabled = true;
            generateBtn.style.opacity = '0.5';
            generateBtn.style.cursor = 'not-allowed';
            
            try {
                const res = await fetch('{{ route("openrouter.text.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('responseText').textContent = data.text;
                    
                    if (data.usage) {
                        document.getElementById('usage').innerHTML = 
                            `<strong>Usage:</strong> ${data.usage.promptTokens} prompt tokens, ${data.usage.completionTokens} completion tokens (Total: ${data.usage.totalTokens})`;
                    }
                    
                    response.classList.remove('hidden');
                } else {
                    error.querySelector('p').textContent = data.error || 'An error occurred';
                    error.classList.remove('hidden');
                }
            } catch (err) {
                error.querySelector('p').textContent = 'Network error: ' + err.message;
                error.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
                generateBtn.disabled = false;
                generateBtn.style.opacity = '1';
                generateBtn.style.cursor = 'pointer';
            }
        });
    </script>
</x-app-layout>
