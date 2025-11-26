<?php

namespace App\Livewire\Schedule;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Title, Layout};
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

#[Title('Manajemen Jadwal')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $filterMonth = '';
    public string $filterYear = '';
    public string $search = '';

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterMonth' => ['except' => ''],
        'filterYear' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }

    public function updatingFilterYear()
    {
        $this->resetPage();
    }

    public function publish(int $scheduleId): void
    {
        try {
            $schedule = Schedule::findOrFail($scheduleId);
            
            if ($schedule->status === 'published') {
                $this->dispatch('alert', type: 'warning', message: 'Jadwal sudah dipublish.');
                return;
            }
            
            $schedule->update(['status' => 'published']);
            
            $this->dispatch('alert', type: 'success', message: 'Jadwal berhasil dipublish!');
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Gagal publish jadwal: ' . $e->getMessage());
        }
    }

    public function delete(int $scheduleId): void
    {
        try {
            DB::beginTransaction();
            
            $schedule = Schedule::findOrFail($scheduleId);
            
            // Delete assignments first
            $schedule->assignments()->delete();
            
            // Delete schedule
            $schedule->delete();
            
            DB::commit();
            
            $this->dispatch('alert', type: 'success', message: 'Jadwal berhasil dihapus!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $schedules = Schedule::query()
            ->with(['assignments'])
            ->withCount('assignments')
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMonth, fn($q) => $q->whereMonth('week_start_date', $this->filterMonth))
            ->when($this->filterYear, fn($q) => $q->whereYear('week_start_date', $this->filterYear))
            ->when($this->search, fn($q) => $q->where('notes', 'like', "%{$this->search}%"))
            ->latest('week_start_date')
            ->paginate(10);

        return view('livewire.schedule.index', compact('schedules'));
    }
}
