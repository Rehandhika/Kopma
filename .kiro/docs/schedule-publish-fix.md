# Perbaikan Schedule Publish/Submit Issue

## Masalah yang Ditemukan

Pengguna tidak bisa publish atau submit jadwal meskipun form sudah terisi semua.

## Analisis Root Cause

Setelah mempelajari keseluruhan logika backend dari `http://kopma.test/schedule/create`, ditemukan beberapa masalah:

### 1. **Validasi Form Tidak Berfungsi dengan Benar**
- Method `$this->validate()` dipanggil tanpa parameter
- Rules didefinisikan sebagai property static, bukan method
- Tidak ada error handling yang jelas untuk validation errors

### 2. **Redirect Tidak Bekerja di Livewire**
- Menggunakan `return redirect()` yang tidak kompatibel dengan Livewire
- Seharusnya menggunakan `$this->redirect()` method dari Livewire

### 3. **State Management Issue**
- Property `$isSaving` tidak di-reset dengan benar saat error
- Bisa menyebabkan tombol tetap disabled setelah error

### 4. **Kurangnya Error Logging**
- Sulit untuk debug karena tidak ada logging yang cukup
- User tidak mendapat feedback yang jelas saat terjadi error

## Perbaikan yang Dilakukan

### 1. **Perbaikan Validasi**

**File:** `app/Livewire/Schedule/CreateSchedule.php`

```php
// Sebelum
protected $rules = [
    'weekStartDate' => 'required|date',
    'weekEndDate' => 'required|date|after:weekStartDate',
    'notes' => 'nullable|string|max:500',
];

public function publish()
{
    $this->validate(); // Tidak ada parameter!
    // ...
}

// Sesudah
protected function rules()
{
    return [
        'weekStartDate' => 'required|date',
        'weekEndDate' => 'required|date|after:weekStartDate',
        'notes' => 'nullable|string|max:500',
    ];
}

protected $messages = [
    'weekStartDate.required' => 'Tanggal mulai harus diisi.',
    'weekStartDate.date' => 'Format tanggal mulai tidak valid.',
    'weekEndDate.required' => 'Tanggal selesai harus diisi.',
    'weekEndDate.date' => 'Format tanggal selesai tidak valid.',
    'weekEndDate.after' => 'Tanggal selesai harus setelah tanggal mulai.',
    'notes.max' => 'Catatan maksimal 500 karakter.',
];

public function publish()
{
    try {
        $this->validate([
            'weekStartDate' => 'required|date',
            'weekEndDate' => 'required|date|after:weekStartDate',
            'notes' => 'nullable|string|max:500',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $this->dispatch('alert', type: 'error', message: 'Mohon periksa kembali tanggal yang diisi.');
        throw $e;
    }
    // ...
}
```

### 2. **Perbaikan Redirect**

```php
// Sebelum
return redirect()->route('schedule.index');

// Sesudah
$this->redirect(route('schedule.index'), navigate: true);
```

### 3. **Perbaikan State Management**

```php
// Tambahkan di mount()
public function mount()
{
    // ...
    $this->isSaving = false;
    $this->isLoading = false;
    // ...
}

// Tambahkan method reset
public function resetSavingState(): void
{
    $this->isSaving = false;
    $this->isLoading = false;
}

// Pastikan reset di setiap catch block
catch (\Exception $e) {
    DB::rollBack();
    $this->isSaving = false; // Reset state
    // ...
}
```

### 4. **Penambahan Logging Comprehensive**

```php
public function publish()
{
    \Illuminate\Support\Facades\Log::info('Publish method called', [
        'user_id' => auth()->id(),
        'weekStartDate' => $this->weekStartDate,
        'weekEndDate' => $this->weekEndDate,
        'totalAssignments' => $this->totalAssignments,
    ]);
    
    // ... logging di setiap step penting
    
    \Illuminate\Support\Facades\Log::info('Creating schedule');
    $schedule = $scheduleService->createSchedule([...]);
    
    \Illuminate\Support\Facades\Log::info('Adding assignments', ['total' => count($this->assignments)]);
    // ...
}
```

### 5. **Perbaikan View (Blade Template)**

**File:** `resources/views/livewire/schedule/create-schedule.blade.php`

```blade
{{-- Sebelum --}}
<x-ui.button 
    wire:click="publish" 
    variant="primary"
    icon="check-circle"
    wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="publish">Publish Schedule</span>
    <span wire:loading wire:target="publish">Publishing...</span>
</x-ui.button>

{{-- Sesudah --}}
<x-ui.button 
    wire:click.prevent="publish" 
    variant="primary"
    icon="check-circle"
    :disabled="$isSaving"
    wire:loading.attr="disabled"
    wire:target="saveDraft,publish"
    type="button">
    <span wire:loading.remove wire:target="publish">Publish Schedule</span>
    <span wire:loading wire:target="publish">Publishing...</span>
</x-ui.button>
```

Perubahan:
- Tambah `wire:click.prevent` untuk mencegah default behavior
- Tambah `:disabled="$isSaving"` untuk bind ke state
- Tambah `wire:target="saveDraft,publish"` untuk disable saat salah satu action berjalan
- Tambah `type="button"` untuk memastikan bukan submit button

### 6. **Penambahan Debug Info**

```blade
{{-- Debug Info (Remove in production) --}}
@if(config('app.debug'))
<div class="mt-4 p-4 bg-gray-100 rounded text-xs">
    <strong>Debug Info:</strong><br>
    isSaving: {{ $isSaving ? 'true' : 'false' }}<br>
    isLoading: {{ $isLoading ? 'true' : 'false' }}<br>
    totalAssignments: {{ $totalAssignments }}<br>
    coverageRate: {{ $coverageRate }}%<br>
    weekStartDate: {{ $weekStartDate }}<br>
    weekEndDate: {{ $weekEndDate }}
</div>
@endif
```

### 7. **Penambahan JavaScript Debugging**

```javascript
@push('scripts')
<script>
    // Debug Livewire events
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized for CreateSchedule');
        
        Livewire.on('alert', (event) => {
            console.log('Alert event received:', event);
        });
    });
    
    // Log button clicks for debugging
    document.addEventListener('DOMContentLoaded', function() {
        console.log('CreateSchedule page loaded');
        
        document.addEventListener('click', function(e) {
            if (e.target.closest('[wire\\:click*="publish"]') || e.target.closest('[wire\\:click*="saveDraft"]')) {
                console.log('Button clicked:', e.target);
                console.log('Wire click attribute:', e.target.getAttribute('wire:click'));
            }
        });
    });
</script>
@endpush
```

## Cara Testing

1. **Buka halaman create schedule:**
   ```
   http://kopma.test/schedule/create
   ```

2. **Isi form dengan data valid:**
   - Pilih tanggal mulai (Senin)
   - Pilih tanggal selesai (Kamis, 3 hari setelah tanggal mulai)
   - Tambahkan minimal 1 assignment (50% coverage untuk publish)

3. **Cek browser console:**
   - Buka Developer Tools (F12)
   - Lihat tab Console untuk log debugging
   - Pastikan tidak ada error JavaScript

4. **Cek Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   - Lihat log saat klik tombol Publish
   - Pastikan semua step tercatat dengan baik

5. **Test scenario:**
   - ✅ Publish dengan data valid dan coverage >= 50%
   - ✅ Publish dengan data valid tapi coverage < 50% (harus error)
   - ✅ Publish tanpa assignment (harus error)
   - ✅ Save Draft dengan minimal 1 assignment
   - ✅ Save Draft tanpa assignment (harus warning)

## Best Practices yang Diterapkan

1. **Proper Validation:**
   - Validasi di level Livewire component
   - Validasi di level Service
   - Validasi di level Model
   - Custom error messages yang jelas

2. **Error Handling:**
   - Try-catch di setiap method yang bisa error
   - Rollback transaction saat error
   - Reset state saat error
   - Log error dengan detail lengkap

3. **User Feedback:**
   - Alert notification untuk setiap action
   - Loading state yang jelas
   - Error message yang informatif
   - Debug info saat development mode

4. **State Management:**
   - Reset state di mount()
   - Reset state saat error
   - Proper loading state management
   - Prevent double submission

5. **Logging:**
   - Log setiap step penting
   - Log dengan context yang lengkap
   - Log level yang sesuai (info, warning, error)
   - Structured logging untuk easy debugging

## Additional Fix: Notification Type Error

### Masalah
Setelah perbaikan awal, ditemukan error baru saat publish:
```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'type' at row 1
```

### Root Cause
Kolom `type` di tabel `notifications` adalah ENUM dengan nilai terbatas:
```php
$table->enum('type', ['info', 'warning', 'error', 'success']);
```

Tapi kode mencoba menyimpan `'schedule_published'` yang bukan salah satu nilai enum.

### Solusi
**File:** `app/Services/ScheduleService.php`

```php
// Sebelum
Notification::create([
    'user_id' => $userId,
    'type' => 'schedule_published', // ❌ Invalid enum value
    'title' => 'Jadwal Shift Dipublikasikan',
    'message' => $message,
    'data' => json_encode([
        'schedule_id' => $schedule->id,
        'assignments_count' => $userAssignments->count(),
    ]),
]);

// Sesudah
Notification::create([
    'user_id' => $userId,
    'type' => 'info', // ✅ Valid enum value
    'title' => 'Jadwal Shift Dipublikasikan',
    'message' => $message,
    'data' => json_encode([
        'schedule_id' => $schedule->id,
        'assignments_count' => $userAssignments->count(),
        'notification_type' => 'schedule_published', // Store actual type in data
    ]),
]);
```

## Checklist Verifikasi

- [x] Validasi form berfungsi dengan benar
- [x] Redirect berfungsi setelah publish/save
- [x] State management tidak stuck
- [x] Error handling comprehensive
- [x] Logging lengkap untuk debugging
- [x] User feedback jelas
- [x] No syntax errors
- [x] Best practices diterapkan
- [x] Notification type menggunakan nilai enum yang valid

## Notes

- Debug info akan muncul hanya saat `APP_DEBUG=true`
- JavaScript console logging membantu debug client-side issues
- Laravel log membantu debug server-side issues
- Pastikan untuk remove debug code sebelum production

## Referensi

- [Livewire Validation](https://livewire.laravel.com/docs/validation)
- [Livewire Redirects](https://livewire.laravel.com/docs/redirecting)
- [Laravel Logging](https://laravel.com/docs/logging)
- [Laravel Validation](https://laravel.com/docs/validation)
