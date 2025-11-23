<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Test Page - SIKOPMA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🧪 Alpine.js Test Page</h1>
            <p class="text-gray-600">Halaman ini untuk testing apakah Alpine.js berfungsi dengan benar</p>
        </div>

        <!-- Test 1: Basic x-data and x-show -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 1: Basic Toggle (x-data, x-show, @click)</h2>
            <div x-data="{ open: false }">
                <button 
                    @click="open = !open"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Toggle Content
                </button>
                <div x-show="open" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    ✅ Alpine.js berfungsi! Konten ini muncul karena x-show bekerja.
                </div>
            </div>
        </div>

        <!-- Test 2: x-collapse Plugin -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 2: Collapse Plugin (x-collapse)</h2>
            <div x-data="{ expanded: false }">
                <button 
                    @click="expanded = !expanded"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                    <span x-text="expanded ? 'Collapse' : 'Expand'"></span>
                </button>
                <div x-show="expanded" x-collapse class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    ✅ Plugin @alpinejs/collapse berfungsi! Perhatikan animasi smooth saat expand/collapse.
                </div>
            </div>
        </div>

        <!-- Test 3: x-transition -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 3: Transitions (x-transition)</h2>
            <div x-data="{ visible: false }">
                <button 
                    @click="visible = !visible"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                >
                    Toggle with Transition
                </button>
                <div 
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg"
                >
                    ✅ Transitions berfungsi! Perhatikan fade dan scale animation.
                </div>
            </div>
        </div>

        <!-- Test 4: Alpine Store -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 4: Alpine Store (notifications)</h2>
            <div x-data>
                <button 
                    @click="$store.notifications.add('Test notification berhasil!', 'success')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                >
                    Trigger Notification
                </button>
                <p class="mt-2 text-sm text-gray-600">
                    Klik tombol di atas, notification harus muncul di kanan atas (jika ada di layout)
                </p>
            </div>
        </div>

        <!-- Test 5: Alpine Data Component -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 5: Alpine Data Component (dropdown)</h2>
            <div x-data="dropdown()" @click.away="close()">
                <button 
                    @click="toggle()"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700"
                >
                    Toggle Dropdown
                </button>
                <div 
                    x-show="open"
                    x-transition
                    class="mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg"
                >
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 1</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 2</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 3</a>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    ✅ Click away to close harus berfungsi
                </p>
            </div>
        </div>

        <!-- Test 6: x-model -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 6: Two-way Binding (x-model)</h2>
            <div x-data="{ message: 'Hello Alpine!' }">
                <input 
                    type="text" 
                    x-model="message"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Ketik sesuatu..."
                >
                <p class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <strong>Output:</strong> <span x-text="message"></span>
                </p>
            </div>
        </div>

        <!-- Test 7: x-for -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 7: Loops (x-for)</h2>
            <div x-data="{ 
                items: ['Item 1', 'Item 2', 'Item 3'],
                newItem: ''
            }">
                <div class="flex gap-2 mb-4">
                    <input 
                        type="text" 
                        x-model="newItem"
                        @keyup.enter="items.push(newItem); newItem = ''"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                        placeholder="Tambah item (tekan Enter)"
                    >
                    <button 
                        @click="items.push(newItem); newItem = ''"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Add
                    </button>
                </div>
                <ul class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <li class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <span x-text="item"></span>
                            <button 
                                @click="items.splice(index, 1)"
                                class="text-red-600 hover:text-red-800"
                            >
                                ✕
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Test 8: x-if -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test 8: Conditional Rendering (x-if)</h2>
            <div x-data="{ count: 0 }">
                <div class="flex gap-2 mb-4">
                    <button 
                        @click="count++"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Increment
                    </button>
                    <button 
                        @click="count--"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Decrement
                    </button>
                    <button 
                        @click="count = 0"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                    >
                        Reset
                    </button>
                </div>
                <div class="text-2xl font-bold mb-4">Count: <span x-text="count"></span></div>
                <template x-if="count > 0">
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        ✅ Count is positive!
                    </div>
                </template>
                <template x-if="count < 0">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        ⚠️ Count is negative!
                    </div>
                </template>
                <template x-if="count === 0">
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        ℹ️ Count is zero
                    </div>
                </template>
            </div>
        </div>

        <!-- Console Check -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">🔍 Console Check</h2>
            <p class="text-gray-600 mb-4">Buka Developer Console (F12) dan jalankan command berikut:</p>
            <div class="space-y-2 font-mono text-sm">
                <div class="p-3 bg-gray-900 text-green-400 rounded">
                    <div>> Alpine</div>
                    <div class="text-gray-500">// Harus return object, bukan undefined</div>
                </div>
                <div class="p-3 bg-gray-900 text-green-400 rounded">
                    <div>> Alpine.version</div>
                    <div class="text-gray-500">// Harus return "3.15.1" atau similar</div>
                </div>
                <div class="p-3 bg-gray-900 text-green-400 rounded">
                    <div>> Alpine.store('notifications')</div>
                    <div class="text-gray-500">// Harus return object dengan method add() dan remove()</div>
                </div>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-4">✅ Test Results</h2>
            <p class="text-lg mb-4">Jika semua test di atas berfungsi, maka Alpine.js sudah bekerja dengan sempurna!</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="font-semibold mb-2">✅ Harus Berfungsi:</div>
                    <ul class="space-y-1 opacity-90">
                        <li>• Basic toggle (x-show)</li>
                        <li>• Collapse animation</li>
                        <li>• Transitions</li>
                        <li>• Alpine stores</li>
                    </ul>
                </div>
                <div>
                    <div class="font-semibold mb-2">✅ Harus Berfungsi:</div>
                    <ul class="space-y-1 opacity-90">
                        <li>• Data components</li>
                        <li>• Two-way binding</li>
                        <li>• Loops (x-for)</li>
                        <li>• Conditionals (x-if)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back to App -->
        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Toast Notification Area (for testing) -->
    <div 
        x-data="{ items: [] }"
        x-init="$watch('$store.notifications.items', value => items = value)"
        class="fixed top-4 right-4 z-50 space-y-2"
    >
        <template x-for="item in items" :key="item.id">
            <div 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <template x-if="item.type === 'success'">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="item.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button 
                                @click="$store.notifications.remove(item.id)"
                                class="inline-flex text-gray-400 hover:text-gray-500"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
