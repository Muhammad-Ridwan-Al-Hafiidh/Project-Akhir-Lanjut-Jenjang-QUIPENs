<?php

namespace App\Http\Livewire\Factory\Question\UploadFileQuestion;

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
            'max_size' => 1024,
            'min_size' => 128,
            'file_type' => 'pdf,word,excel,images'
        ];
        $this->correctAnswer = [];
        
        if (!empty($this->question)) {
            $this->setValueWithQuestion();
        }
    }

    public function render()
    {
        return view('livewire.factory.question.upload-file-question.create');
    }
}