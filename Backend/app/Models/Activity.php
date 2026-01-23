<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Vilka fält som kan mass-assignment
    protected $fillable = ['name'];

    // Relation till workouts (om ni vill använda det senare)
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
