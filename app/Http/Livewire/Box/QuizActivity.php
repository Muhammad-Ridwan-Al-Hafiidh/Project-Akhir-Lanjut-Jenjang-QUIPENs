<?php

namespace App\Http\Livewire\Box;

use App\Models\Quiz;
use Livewire\Component;

class QuizActivity extends Component
{
    public $session;

    public function render()
    {
        $quizes = Quiz::query()
            ->orderBy('title')
            ->limit(100)
            ->get();

        return view('livewire.box.quiz-activity', compact('quizes'));
    }
}