<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_start_date',
        'week_end_date',
        'status',
        'generated_by',
        'generated_at',
        'published_at',
        'notes',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date' => 'date',
        'generated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function assignments()
    {
        return $this->hasMany(ScheduleAssignment::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCurrentWeek($query)
    {
        $monday = Carbon::now()->startOfWeek();
        return $query->where('week_start_date', $monday->toDateString());
    }

    // Helpers
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canEdit(): bool
    {
        return $this->status === 'draft';
    }
}
