<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use App\Models\ScheduleAssignment;
use App\Models\Attendance;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckInOut extends Component
{
    public $currentSchedule;
    public $currentAttendance;
    public $checkInTime;
    public $checkOutTime;
    public $notes = '';

    public function mount()
    {
        $this->loadCurrentSchedule();
    }

    public function loadCurrentSchedule()
    {
        $user = auth()->user();
        $now = now();

        // Find current schedule assignment
        $this->currentSchedule = ScheduleAssignment::where('user_id', $user->id)
            ->where('date', today())
            ->where('status', 'scheduled')
            ->where('time_start', '<=', $now->format('H:i:s'))
            ->where('time_end', '>=', $now->format('H:i:s'))
            ->first();

        if ($this->currentSchedule) {
            $this->currentAttendance = Attendance::where('user_id', $user->id)
                ->where('schedule_assignment_id', $this->currentSchedule->id)
                ->first();

            if ($this->currentAttendance) {
                $this->checkInTime = $this->currentAttendance->check_in?->format('H:i');
                $this->checkOutTime = $this->currentAttendance->check_out?->format('H:i');
                $this->notes = $this->currentAttendance->notes ?? '';
            }
        }
    }

    public function checkIn()
    {
        if (!$this->currentSchedule) {
            session()->flash('error', 'Tidak ada jadwal aktif saat ini.');
            return;
        }

        if ($this->currentAttendance && $this->currentAttendance->check_in) {
            session()->flash('error', 'Anda sudah check-in.');
            return;
        }

        $now = now();

        $this->currentAttendance = Attendance::create([
            'user_id' => auth()->id(),
            'schedule_assignment_id' => $this->currentSchedule->id,
            'date' => today(),
            'check_in' => $now,
            'notes' => $this->notes,
        ]);

        $this->checkInTime = $now->format('H:i');

        // Notify user
        NotificationService::send(
            auth()->user(),
            'check_in_success',
            'Check-in Berhasil',
            "Check-in berhasil pada {$now->format('H:i')} untuk jadwal {$this->currentSchedule->day_label} {$this->currentSchedule->session_label}"
        );

        session()->flash('success', 'Check-in berhasil!');
        $this->loadCurrentSchedule();
    }

    public function checkOut()
    {
        if (!$this->currentAttendance || !$this->currentAttendance->check_in) {
            session()->flash('error', 'Anda belum check-in.');
            return;
        }

        if ($this->currentAttendance->check_out) {
            session()->flash('error', 'Anda sudah check-out.');
            return;
        }

        $now = now();

        $this->currentAttendance->update([
            'check_out' => $now,
            'notes' => $this->notes,
        ]);

        $this->checkOutTime = $now->format('H:i');

        // Calculate working hours
        $checkIn = Carbon::parse($this->currentAttendance->check_in);
        $checkOut = Carbon::parse($this->currentAttendance->check_out);
        $workingHours = $checkIn->diffInMinutes($checkOut) / 60;

        // Notify user
        NotificationService::send(
            auth()->user(),
            'check_out_success',
            'Check-out Berhasil',
            "Check-out berhasil pada {$now->format('H:i')}. Total jam kerja: " . number_format($workingHours, 2) . " jam"
        );

        session()->flash('success', 'Check-out berhasil!');
        $this->loadCurrentSchedule();
    }

    public function updateNotes()
    {
        if ($this->currentAttendance) {
            $this->currentAttendance->update(['notes' => $this->notes]);
            session()->flash('success', 'Catatan berhasil diperbarui.');
        }
    }

    public function render()
    {
        return view('livewire.attendance.check-in-out')
            ->layout('layouts.app');
    }
}
