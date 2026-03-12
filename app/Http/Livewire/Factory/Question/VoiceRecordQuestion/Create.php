<?php

namespace App\Http\Livewire\Factory\Question\VoiceRecordQuestion;

use App\Http\Livewire\Factory\Question\QuestionComponents;

class Create extends QuestionComponents
{
    public function mount($questionTypeId = null, $question = null, $quiz = null, $difficulty = 'medium', $topic = ''): void
    {
        $this->questionTypeId = $questionTypeId ?? 0;
        $this->question = $question;
        $this->quiz = $quiz;
        $this->difficulty = $difficulty;
        $this->topic = $topic;
        
        $this->answers = [
            'min_second' => 120,
            'max_second' => 1200,
        ];
        $this->correctAnswer = [];
        
        if (!empty($this->question)) {
            $this->setValueWithQuestion();
        }
    }

    public function render()
    {
        return view('livewire.factory.question.voice-record-question.create');
    }
}