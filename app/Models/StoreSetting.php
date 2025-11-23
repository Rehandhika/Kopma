<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'is_open',
        'status_reason',
        'last_status_change',
        'auto_status',
        'manual_mode',
        'manual_is_open',
        'manual_close_reason',
        'manual_close_until',
        'manual_open_override',
        'manual_set_by',
        'manual_set_at',
        'operating_hours',
        'contact_phone',
        'contact_email',
        'contact_address',
        'contact_whatsapp',
        'about_text',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'auto_status' => 'boolean',
        'manual_mode' => 'boolean',
        'manual_is_open' => 'boolean',
        'manual_open_override' => 'boolean',
        'last_status_change' => 'datetime',
        'manual_close_until' => 'datetime',
        'manual_set_at' => 'datetime',
        'operating_hours' => 'array',
    ];

    // Relationship
    public function manualSetBy()
    {
        return $this->belongsTo(User::class, 'manual_set_by');
    }
}
