<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'attempt' => 'required|integer|between:0,50',
            'duration' => 'required|integer|between:0,500',
            'is_mentor' => 'required|boolean',
            'is_shuffle' => 'required|boolean',
            'min_pass_score' => 'required|integer|between:0,100',
            'show_question' => 'required',
            'random_question' => 'nullable|integer|between:0,100',
            'easy_questions_count' => 'nullable|integer|between:0,100',
            'medium_questions_count' => 'nullable|integer|between:0,100',
            'hard_questions_count' => 'nullable|integer|between:0,100',
        ];
    }
}
