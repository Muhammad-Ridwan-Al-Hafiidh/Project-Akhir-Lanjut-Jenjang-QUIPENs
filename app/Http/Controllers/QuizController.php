<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Traits\Sequence;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    use Sequence;

    protected array $show_question = [
        'StepByStep', 'OnePage'
    ];

    public function index()
    {
        $this->authorize('quiz.index');
        $quizes = Quiz::paginate();
        return view("contents.admin.quiz.index", compact("quizes"));
    }

    public function create()
    {
        $this->authorize('quiz.create');
        $allTopics = \App\Models\Question::distinct()->pluck('topic')->toArray();
        $show_question = $this->show_question;
        return view('contents.admin.quiz.form', compact('show_question', 'allTopics'));
    }

    public function store(QuizRequest $request)
    {
        $this->authorize('quiz.create');
        
        $topics = $request->input('topics', []);
        $data = $request->except('topics');
        
        $quiz = Quiz::create($data);
        
        if (!empty($topics)) {
            foreach ($topics as $topic) {
                DB::table('quiz_topics')->insert([
                    'quiz_id' => $quiz->id,
                    'topic' => $topic,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        return redirect()
            ->route("quiz.index")
            ->with('success', __('quiz created successfully'));
    }

    public function show(Quiz $quiz)
    {
        $this->authorize('quiz.edit');
        return view('contents.admin.quiz.show', compact("quiz"));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorize('quiz.edit');
        $allTopics = \App\Models\Question::distinct()->pluck('topic')->toArray();
        $show_question = $this->show_question;
        return view('contents.admin.quiz.form', compact('quiz', 'show_question', 'allTopics'));
    }

    public function update(QuizRequest $request, Quiz $quiz)
    {
        $this->authorize('quiz.edit');
        
        $topics = $request->input('topics', []);
        $data = $request->except('topics');
        
        $quiz->update($data);
        
        DB::table('quiz_topics')->where('quiz_id', $quiz->id)->delete();
        
        if (!empty($topics)) {
            foreach ($topics as $topic) {
                DB::table('quiz_topics')->insert([
                    'quiz_id' => $quiz->id,
                    'topic' => $topic,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        return redirect()
            ->route("quiz.index")
            ->with('warning', __('quiz updated successfully'));
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorize('quiz.delete');
        try {
            $quiz->delete();
            return redirect()
                ->route("quiz.index")
                ->with('danger', __('item deleted successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->route("quiz.index")
                ->with('danger', __('Delete is not Completed, Please check child of this quiz'));
        }
    }

    public function orderChangeQuestion(QuizQuestion $from, $move)
    {
        $this->authorize('quiz.update');
        $move_parameters = [
            'up' => ['char' => '<', 'order' => 'desc'],
            'down' => ['char' => '>', 'order' => 'asc']
        ];

        $to = QuizQuestion::where('quiz_id', $from->quiz_id)
            ->where('order', (string)$move_parameters[$move]['char'], $from->order)
            ->orderby('order', (string)$move_parameters[$move]['order'])
            ->first();

        $this->changeOrder($from, $to);

        return redirect()->back();
    }

    public function addQuestionToQuiz(Quiz $parent, Question $question)
    {
        $this->authorize('quiz.create');
        $parent->Questions()->attach(
            $question,
            ['order' => $parent->Questions()->max('order') + 1]
        );
        return redirect()->back();
    }

    public function deleteQuestionAsQuiz(QuizQuestion $quizQuestion)
    {
        $this->authorize('quiz.delete');
        $quizQuestion->delete();
        return redirect()->back();
    }
}
