<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model {

    protected $table = 'workouts';

    protected $fillable = [
        'activity_id',
        'date',
        'description',
        'effort_level',
        'distance_value',
        'distance_unit',
        'duration_value',
        'duration_unit'
    ];

    protected $dates = ['date', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date:d-m-Y',
    ];

    // Relation till aktivitet
    public function activity() {
        return $this->belongsTo(Activity::class);
    }
}
