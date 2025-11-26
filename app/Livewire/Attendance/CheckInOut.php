<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ScheduleAssignment;
use App\Models\Attendance;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CheckInOut extends Component
{
    use WithFileUploads;

    public $currentSchedule;
    public $currentAttendance;
    public $checkInTime;
    public $checkOutTime;
    public $notes = '';
    public $checkInPhoto;
    public $scheduleStatus; // 'active', 'upcoming', 'past'
    public $showPhotoPreview = false;

    protected $rules = [
        'checkInPhoto' => 'required|image|max:5120', // 5MB max
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'checkInPhoto.required' => 'Foto bukti check-in wajib diunggah.',
        'checkInPhoto.image' => 'File harus berupa gambar (jpg, jpeg, png).',
        'checkInPhoto.max' => 'Ukuran foto maksimal 5MB.',
        'notes.max' => 'Catatan maksimal 500 karakter.',
    ];

    public function mount()
    {
        $this->loadCurrentSchedule();
    }

    /**
     * Refresh schedule status (called by polling)
     */
    public function refreshSchedule()
    {
        $this->loadCurrentSchedule();
    }

    /**
     * Load current schedule and attendance data
     */
    public function loadCurrentSchedule()
    {
        $user = auth()->user();
        $now = now();
        $today = today();
        $currentTime = $now->format('H:i:s');

        // Find current active schedule assignment
        // Priority 1: Find assignment that is currently active (within time range)
        $this->currentSchedule = ScheduleAssignment::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'scheduled')
            ->whereHas('schedule', function($query) {
                $query->where('status', 'published');
            })
            ->where('time_start', '<=', $currentTime)
            ->where('time_end', '>=', $currentTime)
            ->with(['schedule', 'user'])
            ->first();

        // Priority 2: If no active assignment, find today's next upcoming assignment
        if (!$this->currentSchedule) {
            $this->currentSchedule = ScheduleAssignment::where('user_id', $user->id)
                ->where('date', $today)
                ->where('status', 'scheduled')
                ->whereHas('schedule', function($query) {
                    $query->where('status', 'published');
                })
                ->where('time_start', '>', $currentTime)
                ->with(['schedule', 'user'])
                ->orderBy('time_start')
                ->first();
        }

        // Priority 3: If no upcoming, check if there's a past assignment today (for late check-in)
        if (!$this->currentSchedule) {
            $this->currentSchedule = ScheduleAssignment::where('user_id', $user->id)
                ->where('date', $today)
                ->where('status', 'scheduled')
                ->whereHas('schedule', function($query) {
                    $query->where('status', 'published');
                })
                ->with(['schedule', 'user'])
                ->orderBy('time_start', 'desc')
                ->first();
        }

        // Load attendance data and determine schedule status
        if ($this->currentSchedule) {
            // Determine schedule status
            $scheduleStart = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_start);
            $scheduleEnd = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_end);
            
            if ($now->between($scheduleStart, $scheduleEnd)) {
                $this->scheduleStatus = 'active';
            } elseif ($now->lt($scheduleStart)) {
                $this->scheduleStatus = 'upcoming';
            } else {
                $this->scheduleStatus = 'past';
            }

            $this->currentAttendance = Attendance::where('user_id', $user->id)
                ->where('schedule_assignment_id', $this->currentSchedule->id)
                ->first();

            if ($this->currentAttendance) {
                $this->checkInTime = $this->currentAttendance->check_in?->format('H:i');
                $this->checkOutTime = $this->currentAttendance->check_out?->format('H:i');
                $this->notes = $this->currentAttendance->notes ?? '';
            }
        } else {
            $this->scheduleStatus = null;
        }
    }

    /**
     * Handle check-in with photo upload
     */
    public function checkIn()
    {
        try {
            // Validate schedule exists
            if (!$this->currentSchedule) {
                throw new \Exception('Tidak ada jadwal aktif saat ini.');
            }

            // Check if already checked in
            if ($this->currentAttendance && $this->currentAttendance->check_in) {
                throw new \Exception('Anda sudah check-in.');
            }

            // Check if schedule is published
            if ($this->currentSchedule->schedule->status !== 'published') {
                throw new \Exception('Jadwal belum dipublikasikan.');
            }

            // Validate timing
            $scheduleStart = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_start);
            $now = now();
            $tolerance = config('sikopma.attendance.allow_early_checkin_minutes', 30);
            
            if ($now->lt($scheduleStart->copy()->subMinutes($tolerance))) {
                throw new \Exception("Belum waktunya check-in. Check-in dapat dilakukan {$tolerance} menit sebelum jadwal dimulai.");
            }

            // Validate photo and notes
            $this->validate();

            // Store photo
            $photoPath = $this->checkInPhoto->store('attendance/check-in', 'public');

            // Create attendance record
            $this->currentAttendance = Attendance::create([
                'user_id' => auth()->id(),
                'schedule_assignment_id' => $this->currentSchedule->id,
                'date' => today(),
                'check_in' => $now,
                'check_in_photo' => $photoPath,
                'notes' => $this->notes,
                'status' => $this->determineAttendanceStatus($now),
            ]);

            $this->checkInTime = $now->format('H:i');

            // Send notification
            NotificationService::send(
                auth()->user(),
                'check_in_success',
                'Check-in Berhasil',
                "Check-in berhasil pada {$now->format('H:i')} untuk jadwal {$this->currentSchedule->day_label} {$this->currentSchedule->session_label}"
            );

            session()->flash('success', 'Check-in berhasil! Waktu: ' . $now->format('H:i'));
            
            // Reset form
            $this->reset(['checkInPhoto', 'showPhotoPreview']);
            
            // Reload schedule data
            $this->loadCurrentSchedule();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show field errors
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('Check-in error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'schedule_id' => $this->currentSchedule?->id,
            ]);
        }
    }

    /**
     * Handle check-out (no photo required)
     */
    public function checkOut()
    {
        try {
            // Validate attendance exists
            if (!$this->currentAttendance || !$this->currentAttendance->check_in) {
                throw new \Exception('Anda belum check-in.');
            }

            // Check if already checked out
            if ($this->currentAttendance->check_out) {
                throw new \Exception('Anda sudah check-out.');
            }

            $now = now();

            // Calculate work hours
            $checkIn = Carbon::parse($this->currentAttendance->check_in);
            $workingMinutes = $checkIn->diffInMinutes($now);
            $workingHours = $workingMinutes / 60;

            // Update attendance record
            $this->currentAttendance->update([
                'check_out' => $now,
                'work_hours' => $workingHours,
                'notes' => $this->notes,
            ]);

            $this->checkOutTime = $now->format('H:i');

            // Send notification
            NotificationService::send(
                auth()->user(),
                'check_out_success',
                'Check-out Berhasil',
                "Check-out berhasil pada {$now->format('H:i')}. Total jam kerja: " . number_format($workingHours, 2) . " jam"
            );

            session()->flash('success', 'Check-out berhasil! Total jam kerja: ' . number_format($workingHours, 2) . ' jam');
            
            // Reload schedule data
            $this->loadCurrentSchedule();

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            \Log::error('Check-out error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'attendance_id' => $this->currentAttendance?->id,
            ]);
        }
    }

    /**
     * Update notes
     */
    public function updateNotes()
    {
        try {
            $this->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            if ($this->currentAttendance) {
                $this->currentAttendance->update(['notes' => $this->notes]);
                session()->flash('success', 'Catatan berhasil diperbarui.');
            } else {
                throw new \Exception('Belum ada data absensi.');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Preview uploaded photo
     */
    public function updatedCheckInPhoto()
    {
        $this->validate([
            'checkInPhoto' => 'image|max:5120',
        ]);

        $this->showPhotoPreview = true;
    }

    /**
     * Remove uploaded photo
     */
    public function removePhoto()
    {
        $this->reset(['checkInPhoto', 'showPhotoPreview']);
    }

    /**
     * Determine attendance status based on check-in time
     */
    private function determineAttendanceStatus(Carbon $checkInTime): string
    {
        $scheduleStart = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_start);
        $lateThreshold = config('sikopma.late_threshold_minutes', 15);

        if ($checkInTime->greaterThan($scheduleStart->copy()->addMinutes($lateThreshold))) {
            return 'late';
        }

        return 'present';
    }

    /**
     * Check if user can check-in now
     */
    public function canCheckInNow(): bool
    {
        if (!$this->currentSchedule) {
            return false;
        }

        if ($this->currentAttendance && $this->currentAttendance->check_in) {
            return false;
        }

        // Allow check-in with tolerance
        $scheduleStart = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_start);
        $now = now();
        $tolerance = config('sikopma.attendance.allow_early_checkin_minutes', 30);
        
        return $now->gte($scheduleStart->copy()->subMinutes($tolerance));
    }

    /**
     * Get time until check-in is available
     */
    public function getTimeUntilCheckIn(): ?string
    {
        if (!$this->currentSchedule || $this->scheduleStatus !== 'upcoming') {
            return null;
        }

        $scheduleStart = Carbon::parse($this->currentSchedule->date->format('Y-m-d') . ' ' . $this->currentSchedule->time_start);
        $tolerance = config('sikopma.attendance.allow_early_checkin_minutes', 30);
        $checkInAvailable = $scheduleStart->copy()->subMinutes($tolerance);
        
        return $checkInAvailable->diffForHumans();
    }

    public function render()
    {
        return view('livewire.attendance.check-in-out', [
            'canCheckIn' => $this->canCheckInNow(),
            'timeUntilCheckIn' => $this->getTimeUntilCheckIn(),
        ])->layout('layouts.app');
    }
}
