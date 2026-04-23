<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizTopic extends Model
{
    use HasFactory;

    protected $table = 'quiz_topics';
    protected $guarded = [];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
