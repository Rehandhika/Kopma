@props([
    'conflicts' => [],
    'show' => true,
])

@php
    $criticalCount = count($conflicts['critical'] ?? []);
    $warningCount = count($conflicts['warning'] ?? []);
    $infoCount = count($conflicts['info'] ?? []);
    $totalCount = $criticalCount + $warningCount + $infoCount;
@endphp

<div class="bg-white rounded-lg shadow-lg border-l-4 border-red-500">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Konflik Terdeteksi
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $totalCount }} masalah ditemukan
                    </p>
                </div>
            </div>
            <button 
                {{ $attributes->merge(['class' => 'text-sm text-gray-600 hover:text-gray-900 font-medium']) }}
            >
                {{ $show ? 'Sembunyikan' : 'Tampilkan' }}
            </button>
        </div>
    </div>

    <!-- Conflict Details -->
    @if($show)
        <div class="px-6 py-4 space-y-3">
            <!-- Critical Conflicts -->
            @if($criticalCount > 0)
                <div class="border-l-4 border-red-500 bg-red-50 rounded-r-lg p-4 transition-all duration-200">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-red-700 font-semibold">{{ $criticalCount }} Konflik Kritis</span>
                    </div>
                    <ul class="space-y-2">
                        @foreach($conflicts['critical'] as $conflict)
                            <li class="text-sm text-red-800 flex items-start">
                                <span class="mr-2">•</span>
                                <span>{{ $conflict['details'] ?? $conflict['message'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Warnings -->
            @if($warningCount > 0)
                <div class="border-l-4 border-yellow-500 bg-yellow-50 rounded-r-lg p-4 transition-all duration-200">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-yellow-700 font-semibold">{{ $warningCount }} Peringatan</span>
                    </div>
                    <ul class="space-y-2">
                        @foreach($conflicts['warning'] as $conflict)
                            <li class="text-sm text-yellow-800 flex items-start">
                                <span class="mr-2">•</span>
                                <span>{{ $conflict['details'] ?? $conflict['message'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info -->
            @if($infoCount > 0)
                <div class="border-l-4 border-blue-500 bg-blue-50 rounded-r-lg p-4 transition-all duration-200">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-blue-700 font-semibold">{{ $infoCount }} Informasi</span>
                    </div>
                    <ul class="space-y-2">
                        @foreach($conflicts['info'] as $conflict)
                            <li class="text-sm text-blue-800 flex items-start">
                                <span class="mr-2">•</span>
                                <span>{{ $conflict['details'] ?? $conflict['message'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>
