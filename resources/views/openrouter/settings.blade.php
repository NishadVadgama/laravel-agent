<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Model Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select AI Provider & Model</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Choose which AI provider and model to use for text generation and streaming demos.
                    </p>

                    <form method="POST" action="{{ route('ai.settings.save') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700 mb-2">
                                Provider
                            </label>
                            <select 
                                id="provider" 
                                name="provider" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="updateModels()"
                                required
                            >
                                <option value="OpenRouter" {{ $currentProvider === 'OpenRouter' ? 'selected' : '' }}>OpenRouter</option>
                                <option value="OpenAI" {{ $currentProvider === 'OpenAI' ? 'selected' : '' }}>OpenAI</option>
                            </select>
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model
                            </label>
                            <select 
                                id="model" 
                                name="model" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                                @foreach($models as $providerName => $providerModels)
                                    <optgroup label="{{ $providerName }}" data-provider="{{ $providerName }}" style="{{ $currentProvider === $providerName ? '' : 'display: none;' }}">
                                        @foreach($providerModels as $modelId => $modelName)
                                            <option value="{{ $modelId }}" {{ $currentModel === $modelId ? 'selected' : '' }}>
                                                {{ $modelName }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Current Selection:</h4>
                            <p class="text-sm text-blue-700">
                                <strong>Provider:</strong> <span id="currentProviderDisplay">{{ $currentProvider }}</span><br>
                                <strong>Model:</strong> <span id="currentModelDisplay">{{ $currentModel }}</span>
                            </p>
                        </div>

                        <div>
                            <button 
                                type="submit"
                                style="background-color: #4F46E5; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#4338CA'"
                                onmouseout="this.style.backgroundColor='#4F46E5'"
                            >
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Available Models</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">OpenAI Models (Cheap)</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li><strong>gpt-4o-mini:</strong> Latest mini model, great performance, very affordable</li>
                                <li><strong>gpt-3.5-turbo:</strong> Fast and efficient, good for most tasks</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">OpenRouter Models (Free)</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li><strong>Qwen 235B:</strong> Large model with excellent performance</li>
                                <li><strong>Llama 3.2 3B:</strong> Meta's efficient small model</li>
                                <li><strong>Phi-3 Mini:</strong> Microsoft's compact but powerful model</li>
                                <li><strong>Gemma 2 9B:</strong> Google's open model</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Try the Demos:</h3>
                    <div class="space-y-3">
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                            <a href="{{ route('openrouter.text') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-base underline">
                                Text Generation Demo →
                            </a>
                            <p class="mt-1 text-sm text-gray-600">Generate text responses (non-streaming)</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                            <a href="{{ route('openrouter.stream') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-base underline">
                                Streaming Demo →
                            </a>
                            <p class="mt-1 text-sm text-gray-600">Stream text responses in real-time</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modelsData = @json($models);

        function updateModels() {
            const provider = document.getElementById('provider').value;
            const modelSelect = document.getElementById('model');
            const optgroups = modelSelect.querySelectorAll('optgroup');
            
            // Hide all optgroups
            optgroups.forEach(group => {
                if (group.dataset.provider === provider) {
                    group.style.display = '';
                    // Select first option in this group
                    const firstOption = group.querySelector('option');
                    if (firstOption) {
                        firstOption.selected = true;
                    }
                } else {
                    group.style.display = 'none';
                }
            });

            updateCurrentDisplay();
        }

        function updateCurrentDisplay() {
            const provider = document.getElementById('provider').value;
            const model = document.getElementById('model').value;
            
            document.getElementById('currentProviderDisplay').textContent = provider;
            document.getElementById('currentModelDisplay').textContent = model;
        }

        // Update display on model change
        document.getElementById('model').addEventListener('change', updateCurrentDisplay);
        
        // Initialize display
        updateCurrentDisplay();
    </script>
</x-app-layout>
