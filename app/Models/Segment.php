<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $table = 'segments';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'type',
        'validation_date', // specific end date
        'validity_days',   // number of days for validity
        'status',
        'client_id',
    ];
 

    /**
     * Relationship: Segment belongs to a Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Scope to get only active segments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the segment is expired based on validation_date
     */
    public function isExpiredByDate()
    {
        if (!$this->validation_date) {
            return false; // no date set
        }
        return now()->gt($this->validation_date);
    }

    /**
     * Check if the segment is expired based on validity_days
     * Assumes 'created_at' is the start date
     */
    public function isExpiredByDays()
    {
        if (!$this->validity_days) {
            return false; // no days set
        }
        $expiryDate = $this->created_at->addDays($this->validity_days);
        return now()->gt($expiryDate);
    }

    /**
     * Check if the segment is expired by either method
     */
    public function isExpired()
    {
        return $this->isExpiredByDate() || $this->isExpiredByDays();
    }
}
