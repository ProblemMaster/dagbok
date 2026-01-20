<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model {
    protected $fillable = [
        'activity_id',
        'date',
        'effort_level',
        'distance_value',
        'distance_unit',
        'duration_value',
        'duration_unit'
    ];

    // Relation till aktivitet
    public function activity() {
        return $this->belongsTo(Activity::class);
    }
}
