<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Selamat datang, {{ auth()->user()->name }}!
                    </h3>
                    <p class="text-sm text-gray-500">
                        NIM: {{ auth()->user()->nim }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Penalty Points -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Poin Penalti
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $penaltyPoints }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Jadwal Hari Ini
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($todaySchedule)
                                    {{ $todaySchedule->session_label }}
                                @else
                                    Tidak ada
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Jadwal Mendatang
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $upcomingSchedules->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unread Notifications -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 12.683A17.925 17.925 0 012 21h13.78a17.925 17.925 0 01-1.868-8.317L12 21l-7.132-8.317z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Notifikasi Baru
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $unreadNotifications->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Schedule Detail -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Jadwal Hari Ini
                </h3>
                @if($todaySchedule)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    {{ $todaySchedule->day_label }}, {{ $todaySchedule->date->format('d M Y') }}
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Sesi: {{ $todaySchedule->session_label }}</p>
                                    <p>Waktu: {{ $todaySchedule->time_start->format('H:i') }} - {{ $todaySchedule->time_end->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Tidak ada jadwal hari ini.</p>
                @endif
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Notifikasi Terbaru
                </h3>
                @if($unreadNotifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($unreadNotifications as $notification)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ Str::limit($notification->message, 100) }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <button
                                        wire:click="markNotificationAsRead({{ $notification->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-500"
                                    >
                                        Tandai dibaca
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Tidak ada notifikasi baru.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Penalties -->
    @if($recentPenalties->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Penalti Terbaru
                </h3>
                <div class="space-y-3">
                    @foreach($recentPenalties as $penalty)
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-red-800">
                                    {{ $penalty->penaltyType->name }}
                                </p>
                                <p class="text-sm text-red-600">
                                    {{ $penalty->description }}
                                </p>
                                <p class="text-xs text-red-500">
                                    {{ $penalty->date->format('d M Y') }} • {{ $penalty->points }} poin
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($penalty->status === 'active') bg-red-100 text-red-800
                                    @elseif($penalty->status === 'appealed') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($penalty->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
