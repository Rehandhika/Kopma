<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'user_id',
        'day',
        'session',
        'date',
        'time_start',
        'time_end',
        'status',
        'swapped_to_user_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function swappedToUser()
    {
        return $this->belongsTo(User::class, 'swapped_to_user_id');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function swapRequests()
    {
        return $this->hasMany(SwapRequest::class, 'requester_assignment_id');
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                     ->where('status', 'scheduled');
    }

    // Helpers
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    public function canSwap(): bool
    {
        return $this->isScheduled() &&
               $this->date->isFuture() &&
               $this->date->diffInHours(now()) > 24;
    }

    public function getSessionLabelAttribute(): string
    {
        $labels = [
            '1' => '08:00 - 12:00',
            '2' => '12:00 - 16:00',
            '3' => '16:00 - 20:00',
        ];
        return $labels[$this->session] ?? '';
    }

    public function getDayLabelAttribute(): string
    {
        $labels = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
        ];
        return $labels[$this->day] ?? '';
    }
}
