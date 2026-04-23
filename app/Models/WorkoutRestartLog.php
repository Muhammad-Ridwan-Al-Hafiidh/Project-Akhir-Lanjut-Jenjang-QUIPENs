<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutRestartLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'topic_levels' => 'json',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}

