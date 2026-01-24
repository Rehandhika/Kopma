@php
    use App\Models\Setting;
    use Illuminate\Support\Facades\Cache;
    
    $isMaintenanceActive = Cache::remember('maintenance_mode', 5, function () {
        return (bool) Setting::get('maintenance_mode', false);
    });
    
    // All authenticated users can see the banner during maintenance
    $showBanner = $isMaintenanceActive && auth()->check();
    
    $maintenanceMessage = Setting::get('maintenance_message', 'Sistem sedang dalam pemeliharaan.');
    $estimatedEnd = Setting::get('maintenance_estimated_end');
@endphp

@if($showBanner)
<div class="bg-gradient-to-r from-red-600 to-red-700 text-white shadow-sm" role="alert" aria-live="polite">
    <div class="px-3 sm:px-4 lg:px-6 py-2">
        {{-- Desktop Layout --}}
        <div class="hidden sm:flex items-center justify-center gap-4">
            {{-- Left: Icon + Maintenance label --}}
            <div class="flex items-center gap-2 px-3 py-1 rounded-lg bg-red-800/50">
                <x-ui.icon name="exclamation-triangle" class="h-4 w-4 text-white" />
                <span class="text-xs font-bold uppercase tracking-wide">Maintenance</span>
            </div>
            
            {{-- Center: Message + Estimated End --}}
            <p class="font-medium text-sm">
                <span>{{ $maintenanceMessage }}</span>
                @if($estimatedEnd)
                    <span class="text-red-200 ml-1">
                        (Estimasi selesai: {{ \Carbon\Carbon::parse($estimatedEnd)->format('d M Y H:i') }})
                    </span>
                @endif
            </p>
        </div>

        {{-- Mobile Layout --}}
        <div class="sm:hidden">
            <div class="flex items-center justify-center gap-2">
                {{-- Icon + Maintenance label --}}
                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-800/50">
                    <x-ui.icon name="exclamation-triangle" class="h-3.5 w-3.5 text-white" />
                    <span class="text-[10px] font-bold uppercase">Maintenance</span>
                </div>
                
                <p class="font-medium text-xs">
                    {{ Str::limit($maintenanceMessage, 20) }}
                </p>
            </div>
            @if($estimatedEnd)
                <p class="text-[10px] text-red-200 mt-1 text-center">
                    Selesai: {{ \Carbon\Carbon::parse($estimatedEnd)->format('d M Y H:i') }}
                </p>
            @endif
        </div>
    </div>
</div>
@endif
