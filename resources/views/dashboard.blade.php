<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Demos with Prism PHP</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Choose your AI provider (OpenAI or OpenRouter) and model, then try out text generation and streaming.
                    </p>
                    <div class="space-y-4">
                        <div class="border-2 border-indigo-200 bg-indigo-50 rounded-lg p-4 hover:border-indigo-400 transition">
                            <a href="{{ route('ai.settings') }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-base underline">
                                ⚙️ Configure AI Settings →
                            </a>
                            <p class="mt-1 text-sm text-gray-600">Select provider (OpenAI/OpenRouter) and model</p>
                        </div>
                        <div class="border-2 border-purple-200 bg-purple-50 rounded-lg p-4 hover:border-purple-400 transition">
                            <a href="{{ route('agent.index') }}" class="text-purple-600 hover:text-purple-800 font-bold text-base underline">
                                🤖 AI Agent with Tools →
                            </a>
                            <p class="mt-1 text-sm text-gray-600">Chat with an AI agent that can search articles using tools</p>
                        </div>
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
</x-app-layout>
