<?php

namespace App\Http\Livewire\Factory\Question\MatchingCaseQuestion;

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
            ['left' => '', 'right' => '']
        ];
        $this->correctAnswer = [];
        
        if (!empty($this->question)) {
            $this->setValueWithQuestion();
        }
    }

    public function addNewAnswer()
    {
        $this->answers[] = ['left' => '', 'right' => ''];
    }

    public function render()
    {
        return view('livewire.factory.question.matching-case-question.create');
    }
}