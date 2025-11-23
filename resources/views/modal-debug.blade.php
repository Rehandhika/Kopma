<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Debug</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Modal Debug Page</h1>

        <!-- Debug Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-4">Debug Checklist</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="check1" class="rounded">
                    <label for="check1">Open browser console (F12)</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="check2" class="rounded">
                    <label for="check2">Type: <code class="bg-gray-800 text-green-400 px-2 py-1 rounded">Alpine</code> (should return object)</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="check3" class="rounded">
                    <label for="check3">Type: <code class="bg-gray-800 text-green-400 px-2 py-1 rounded">Alpine.version</code> (should return "3.15.1")</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="check4" class="rounded">
                    <label for="check4">Check for any errors in console</label>
                </div>
            </div>
        </div>

        <!-- Test 1: Simple Alpine Test -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test 1: Basic Alpine Reactivity</h2>
            <div x-data="{ count: 0 }">
                <p class="mb-2">Counter: <span x-text="count" class="font-bold text-2xl"></span></p>
                <button 
                    @click="count++"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Increment
                </button>
                <p class="text-sm text-gray-600 mt-2">✓ If counter increments, Alpine is working</p>
            </div>
        </div>

        <!-- Test 2: Event Dispatch Test -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test 2: Event Dispatch</h2>
            <div x-data="{ received: false }" @test-event.window="received = true">
                <button 
                    @click="$dispatch('test-event')"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                >
                    Dispatch Event
                </button>
                <p class="mt-2">
                    Event received: 
                    <span x-text="received ? 'YES ✓' : 'NO ✗'" 
                          :class="received ? 'text-green-600 font-bold' : 'text-red-600'"></span>
                </p>
                <p class="text-sm text-gray-600 mt-2">✓ If shows "YES", event dispatch is working</p>
            </div>
        </div>

        <!-- Test 3: Simple Modal Test -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test 3: Manual Modal Control</h2>
            <div x-data="{ modalOpen: false }">
                <button 
                    @click="modalOpen = true"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
                >
                    Open Manual Modal
                </button>

                <!-- Manual Modal -->
                <div 
                    x-show="modalOpen"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    style="display: none;"
                >
                    <div 
                        x-show="modalOpen"
                        @click="modalOpen = false"
                        class="fixed inset-0 bg-gray-500 bg-opacity-75"
                    ></div>
                    
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div 
                            x-show="modalOpen"
                            class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                        >
                            <h3 class="text-lg font-semibold mb-4">Manual Modal</h3>
                            <p class="text-gray-700 mb-4">This is a manually controlled modal using x-data.</p>
                            <button 
                                @click="modalOpen = false"
                                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">✓ If modal opens, basic Alpine modal works</p>
            </div>
        </div>

        <!-- Test 4: Component Modal Test -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test 4: Component Modal with Event</h2>
            
            <!-- Debug buttons -->
            <div class="space-x-2 mb-4">
                <button 
                    @click="$dispatch('open-modal-test')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                >
                    Open via $dispatch
                </button>
                
                <button 
                    onclick="window.dispatchEvent(new CustomEvent('open-modal-test'))"
                    class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700"
                >
                    Open via JS
                </button>
            </div>

            <!-- Event listener debug -->
            <div x-data="{ eventFired: false }" @open-modal-test.window="eventFired = true; console.log('Event received!')">
                <p class="text-sm">
                    Event fired: 
                    <span x-text="eventFired ? 'YES ✓' : 'NO ✗'" 
                          :class="eventFired ? 'text-green-600 font-bold' : 'text-red-600'"></span>
                </p>
            </div>

            <x-ui.modal name="test" title="Component Modal">
                <p class="text-gray-700">If you can see this, the component modal is working!</p>
                <x-slot:footer>
                    <button 
                        @click="$dispatch('close-modal-test')"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                    >
                        Close
                    </button>
                </x-slot:footer>
            </x-ui.modal>
        </div>

        <!-- Console Commands -->
        <div class="bg-gray-800 text-green-400 p-6 rounded-lg font-mono text-sm">
            <h3 class="text-white font-bold mb-4">Console Commands to Try:</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-gray-500">// Check Alpine</span><br>
                    <span class="text-yellow-400">Alpine</span>
                </div>
                <div>
                    <span class="text-gray-500">// Check version</span><br>
                    <span class="text-yellow-400">Alpine.version</span>
                </div>
                <div>
                    <span class="text-gray-500">// Manually trigger modal</span><br>
                    <span class="text-yellow-400">window.dispatchEvent(new CustomEvent('open-modal-test'))</span>
                </div>
                <div>
                    <span class="text-gray-500">// Check for errors</span><br>
                    <span class="text-yellow-400">console.clear()</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Debug script
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== MODAL DEBUG INFO ===');
            console.log('Alpine available:', typeof Alpine !== 'undefined');
            if (typeof Alpine !== 'undefined') {
                console.log('Alpine version:', Alpine.version);
            }
            console.log('Page loaded successfully');
            
            // Listen for all custom events
            window.addEventListener('open-modal-test', function(e) {
                console.log('✓ open-modal-test event detected!', e);
            });
        });
    </script>
</body>
</html>
