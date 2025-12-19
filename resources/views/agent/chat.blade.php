<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Agent - Article Search Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .typing-indicator {
            display: inline-block;
        }
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #9ca3af;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.7;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
        .tool-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            font-size: 0.875rem;
            margin: 0.5rem 0;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        .chat-container {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        .chat-container::-webkit-scrollbar {
            width: 6px;
        }
        .chat-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .chat-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .chat-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-t-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        AI Agent
                    </h1>
                    <p class="text-gray-600 mt-1">Article Search Assistant with Tool Support</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Provider: <span class="font-semibold text-indigo-600">{{ $provider }}</span></div>
                    <div class="text-sm text-gray-500">Model: <span class="font-semibold text-indigo-600">{{ $model }}</span></div>
                    <a href="{{ route('ai.settings') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline mt-1 inline-block">Change Settings</a>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white shadow-lg">
            <div class="chat-container p-6 space-y-4" id="chatContainer">
                <!-- Welcome Message -->
                <div class="message flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl rounded-tl-none p-4 shadow-sm">
                            <p class="text-gray-800">👋 Hello! I'm your AI assistant. I can help you search and find articles in the database.</p>
                            <p class="text-gray-600 mt-2 text-sm">Try asking me:</p>
                            <ul class="text-gray-600 text-sm list-disc list-inside mt-1 space-y-1">
                                <li>"Show me all published articles"</li>
                                <li>"Find articles about Laravel"</li>
                                <li>"What draft articles do we have?"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="bg-white rounded-b-2xl shadow-lg p-6">
            <form id="chatForm" class="flex gap-3">
                <input 
                    type="text" 
                    id="messageInput" 
                    placeholder="Ask me about articles..." 
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    autocomplete="off"
                >
                <button 
                    type="submit" 
                    id="sendButton"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send
                </button>
            </form>
            <div class="mt-3 flex items-center gap-2" id="statusIndicator">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-sm text-gray-600">Ready</span>
            </div>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chatContainer');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const statusIndicator = document.getElementById('statusIndicator');
        
        let conversationHistory = [];
        let isProcessing = false;

        // Add a message to the chat
        function addMessage(role, content, isStreaming = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message flex gap-3';
            
            if (role === 'user') {
                messageDiv.innerHTML = `
                    <div class="flex-1 flex justify-end">
                        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-2xl rounded-tr-none p-4 shadow-sm max-w-[80%]">
                            <p class="whitespace-pre-wrap break-words">${escapeHtml(content)}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                `;
            } else {
                messageDiv.innerHTML = `
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl rounded-tl-none p-4 shadow-sm" id="${isStreaming ? 'streamingMessage' : ''}">
                            <p class="text-gray-800 whitespace-pre-wrap break-words">${content}</p>
                        </div>
                    </div>
                `;
            }
            
            chatContainer.appendChild(messageDiv);
            scrollToBottom();
            return messageDiv;
        }

        // Add typing indicator
        function addTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message flex gap-3';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl rounded-tl-none p-4 shadow-sm">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            `;
            chatContainer.appendChild(typingDiv);
            scrollToBottom();
        }

        // Remove typing indicator
        function removeTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) {
                indicator.remove();
            }
        }

        // Add tool badge
        function addToolBadge(toolName, status = 'calling') {
            const badge = document.createElement('div');
            badge.className = 'tool-badge';
            badge.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>${status === 'calling' ? '🔧 Using tool: ' : '✅ Tool result: '}${toolName}</span>
            `;
            
            const lastMessage = chatContainer.lastElementChild;
            if (lastMessage) {
                lastMessage.querySelector('.flex-1 > div').appendChild(badge);
            }
        }

        // Update status
        function updateStatus(status, color = 'green') {
            statusIndicator.innerHTML = `
                <div class="w-2 h-2 rounded-full bg-${color}-500"></div>
                <span class="text-sm text-gray-600">${status}</span>
            `;
        }

        // Scroll to bottom
        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Handle form submission
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (isProcessing) return;
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Add user message
            addMessage('user', message);
            conversationHistory.push({ role: 'user', content: message });
            
            // Clear input
            messageInput.value = '';
            isProcessing = true;
            sendButton.disabled = true;
            
            // Add typing indicator
            addTypingIndicator();
            updateStatus('Processing...', 'yellow');
            
            try {
                // Create fetch URL
                const baseUrl = '{{ route('agent.chat') }}';
                
                console.log('Connecting to:', baseUrl);
                
                // Use POST with fetch for better cookie and CSRF handling
                const response = await fetch(baseUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'text/event-stream',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message: message,
                        history: conversationHistory.slice(0, -1)
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let messageElement = null;
                let assistantResponse = '';
                
                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    
                    const chunk = decoder.decode(value);
                    const lines = chunk.split('\n');
                    
                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            const data = JSON.parse(line.substring(6));
                            
                            if (data.type === 'delta') {
                                if (!messageElement) {
                                    removeTypingIndicator();
                                    messageElement = addMessage('assistant', data.text, true);
                                } else {
                                    const contentDiv = document.getElementById('streamingMessage');
                                    if (contentDiv) {
                                        contentDiv.querySelector('p').textContent += data.text;
                                    }
                                }
                                assistantResponse += data.text;
                            } else if (data.type === 'tool_call') {
                                if (!messageElement) {
                                    removeTypingIndicator();
                                    messageElement = addMessage('assistant', '', true);
                                }
                                addToolBadge(data.tool, 'calling');
                                updateStatus(`Using tool: ${data.tool}`, 'blue');
                            } else if (data.type === 'tool_result') {
                                updateStatus(`Tool completed: ${data.tool}`, 'green');
                            } else if (data.type === 'done') {
                                conversationHistory.push({ role: 'assistant', content: assistantResponse });
                                isProcessing = false;
                                sendButton.disabled = false;
                                updateStatus('Ready', 'green');
                                messageInput.focus();
                            } else if (data.type === 'error') {
                                removeTypingIndicator();
                                addMessage('assistant', `❌ Error: ${data.error}`);
                                isProcessing = false;
                                sendButton.disabled = false;
                                updateStatus('Error', 'red');
                            }
                        }
                    }
                }
                
            } catch (error) {
                console.error('Error:', error);
                removeTypingIndicator();
                addMessage('assistant', `❌ Error: ${error.message}`);
                isProcessing = false;
                sendButton.disabled = false;
                updateStatus('Error', 'red');
            }
        });

        // Focus input on load
        messageInput.focus();
    </script>
</body>
</html>
