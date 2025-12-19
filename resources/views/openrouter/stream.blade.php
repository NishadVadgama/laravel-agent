<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Streaming Demo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">
                                Using: <span class="font-semibold text-indigo-600">{{ $provider }}</span> - <span class="font-semibold text-indigo-600">{{ $model }}</span> (Streaming)
                            </p>
                        </div>
                        <a href="{{ route('ai.settings') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                            Change Model →
                        </a>
                    </div>

                    <form id="streamForm" class="space-y-4">
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
                                placeholder="e.g., Tell me a long story about AI and the future of humanity..."
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
                                Start Streaming
                            </button>
                            <button 
                                type="button" 
                                id="stopBtn"
                                style="display: none; background-color: #DC2626; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: background-color 0.2s; margin-left: 8px;"
                                onmouseover="this.style.backgroundColor='#B91C1C'"
                                onmouseout="this.style.backgroundColor='#DC2626'"
                            >
                                Stop
                            </button>
                        </div>
                    </form>

                    <div id="streaming" class="hidden mt-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Streaming response...
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
                        <a href="{{ route('openrouter.text') }}" class="text-indigo-600 hover:text-indigo-800 underline block">
                            Try the Text Generation Demo →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let eventSource = null;

        document.getElementById('streamForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const prompt = document.getElementById('prompt').value;
            const generateBtn = document.getElementById('generateBtn');
            const stopBtn = document.getElementById('stopBtn');
            const streaming = document.getElementById('streaming');
            const error = document.getElementById('error');
            const response = document.getElementById('response');
            const responseText = document.getElementById('responseText');
            
            // Reset UI
            streaming.classList.remove('hidden');
            error.classList.add('hidden');
            response.classList.remove('hidden');
            responseText.textContent = '';
            generateBtn.disabled = true;
            generateBtn.style.opacity = '0.5';
            generateBtn.style.cursor = 'not-allowed';
            stopBtn.style.display = 'inline-block';
            
            try {
                // Close existing connection if any
                if (eventSource) {
                    eventSource.close();
                }
                
                // Create new EventSource connection
                const url = new URL('{{ route("openrouter.stream.generate") }}', window.location.origin);
                url.searchParams.append('prompt', prompt);
                url.searchParams.append('_token', '{{ csrf_token() }}');
                
                eventSource = new EventSource(url);
                
                eventSource.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    
                    if (data.type === 'delta') {
                        responseText.textContent += data.text;
                    } else if (data.type === 'done') {
                        streaming.classList.add('hidden');
                        generateBtn.disabled = false;
                        generateBtn.style.opacity = '1';
                        generateBtn.style.cursor = 'pointer';
                        stopBtn.style.display = 'none';
                        eventSource.close();
                        eventSource = null;
                    } else if (data.type === 'error') {
                        error.querySelector('p').textContent = data.error;
                        error.classList.remove('hidden');
                        streaming.classList.add('hidden');
                        generateBtn.disabled = false;
                        generateBtn.style.opacity = '1';
                        generateBtn.style.cursor = 'pointer';
                        stopBtn.style.display = 'none';
                        eventSource.close();
                        eventSource = null;
                    }
                };
                
                eventSource.onerror = (err) => {
                    console.error('EventSource error:', err);
                    error.querySelector('p').textContent = 'Connection error occurred';
                    error.classList.remove('hidden');
                    streaming.classList.add('hidden');
                    generateBtn.disabled = false;
                    generateBtn.style.opacity = '1';
                    generateBtn.style.cursor = 'pointer';
                    stopBtn.style.display = 'none';
                    if (eventSource) {
                        eventSource.close();
                        eventSource = null;
                    }
                };
                
            } catch (err) {
                error.querySelector('p').textContent = 'Error: ' + err.message;
                error.classList.remove('hidden');
                streaming.classList.add('hidden');
                generateBtn.disabled = false;
                generateBtn.style.opacity = '1';
                generateBtn.style.cursor = 'pointer';
                stopBtn.style.display = 'none';
            }
        });
        
        document.getElementById('stopBtn').addEventListener('click', () => {
            if (eventSource) {
                eventSource.close();
                eventSource = null;
            }
            
            document.getElementById('streaming').classList.add('hidden');
            document.getElementById('generateBtn').disabled = false;
            document.getElementById('generateBtn').style.opacity = '1';
            document.getElementById('generateBtn').style.cursor = 'pointer';
            document.getElementById('stopBtn').style.display = 'none';
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (eventSource) {
                eventSource.close();
            }
        });
    </script>
</x-app-layout>
