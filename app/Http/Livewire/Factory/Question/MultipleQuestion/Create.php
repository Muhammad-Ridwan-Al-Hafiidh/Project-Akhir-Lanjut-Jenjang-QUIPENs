<?php

namespace App\Http\Livewire\Factory\Question\MultipleQuestion;

use App\Http\Livewire\Factory\Question\QuestionComponents;

class Create extends QuestionComponents
{
    public function mount($questionTypeId = null, $question = null, $quiz = null): void
    {
        $this->questionTypeId = $questionTypeId ?? 0;
        $this->question = $question;
        $this->quiz = $quiz;
        
        $this->answers = ['', '', '', ''];
        $this->correctAnswer = ['', '', '', ''];
        
        if (!empty($this->question)) {
            $this->setValueWithQuestion();
        }
    }

    public function render()
    {
        return view('livewire.factory.question.multiple-question.create');
    }
}