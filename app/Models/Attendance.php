<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_assignment_id',
        'date',
        'check_in',
        'check_in_photo',
        'check_out',
        'work_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'work_hours' => 'decimal:2',
    ];

    protected $appends = [
        'check_in_photo_url',
    ];

    /**
     * Get the full URL for check-in photo
     */
    public function getCheckInPhotoUrlAttribute(): ?string
    {
        if (!$this->check_in_photo) {
            return null;
        }

        return \Storage::url($this->check_in_photo);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduleAssignment()
    {
        return $this->belongsTo(ScheduleAssignment::class);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
}
