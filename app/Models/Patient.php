<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    protected $fillable = [
        'serial_number',
        'name',
        'phone',
        'age',
        'gender',
        'address',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class)->latest('visit_date');
    }

    public function latestVisit(): HasOne
    {
        return $this->hasOne(PatientVisit::class)->latestOfMany('visit_date');
    }

    /**
     * Generate the next unique patient serial number, e.g. PT-2026-0001.
     */
    public static function generateSerialNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "PT-{$year}-";

        $last = static::where('serial_number', 'like', $prefix.'%')
            ->orderByDesc('serial_number')
            ->value('serial_number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
