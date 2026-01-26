@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="max-w-md w-full text-center">
        {{-- Lock Icon --}}
        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
            <x-ui.icon name="lock-closed" class="w-10 h-10 text-red-500" />
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Akses Ditolak
        </h1>

        {{-- Message --}}
        <p class="text-gray-600 mb-6">
            {{ session('error', 'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.') }}
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                <x-ui.icon name="home" class="w-5 h-5 mr-2" />
                Kembali ke Dashboard
            </a>
            
            <button onclick="history.back()" 
                    type="button"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5 mr-2" />
                Kembali
            </button>
        </div>

        {{-- Help Text --}}
        <p class="mt-8 text-sm text-gray-500">
            Jika Anda memerlukan akses ke fitur ini, silakan hubungi administrator sistem.
        </p>
    </div>
</div>
@endsection
