<?php

namespace App\Http\Livewire\Activity;

use App\Models\Participant;
use App\Models\Workout;
use App\Models\Question;
use App\Utility\Question\QuestionFactory;
use Livewire\Component;

class Quiz extends Component
{
    public $activity;
    public Participant $participant;
    public Workout $workout;
    public string $style;
    public string $questionsRender = '';
    public $questions; // Collection of Question models

    public function mount()
    {
        $this->questions = collect();

        if ($this->workout && $this->workout->WorkOutQuiz && $this->workout->WorkOutQuiz->count() > 0) {
            foreach ($this->workout->WorkOutQuiz as $log) {
                $q = Question::find($log->question_id);
                if ($q) {
                    $this->questions->push($q);
                    $this->questionsRender .= (string)QuestionFactory::Build($q->QuestionType)->createViewAsLearner($q, $this->workout);
                }
            }
        } else {
            if (!empty($this->activity->Questions())) {
                foreach ($this->activity->Questions as $question) {
                    $this->questions->push($question);
                    $this->questionsRender .= (string)QuestionFactory::Build($question->QuestionType)->createViewAsLearner($question, $this->workout);
                }
            }
        }

        $this->style = $this->activity->show_question;
    }

    public function render()
    {
        return view('livewire.activity.quiz');
    }
}