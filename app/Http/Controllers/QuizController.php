<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Traits\Sequence;
use Illuminate\Http\Request;
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
        return view("contents.admin.quiz.index", compact('quizes'));
    }

    public function create()
    {
        $this->authorize('quiz.create');
        $allTopics = Question::distinct()->pluck('topic')->toArray();
        $show_question = $this->show_question;
        return view('contents.admin.quiz.form', compact('show_question', 'allTopics'));
    }

    public function store(QuizRequest $request)
    {
        $this->authorize('quiz.create');

        $quiz = Quiz::create($this->prepareQuizData($request));
        $this->syncQuizTopics($quiz, $request->input('topics', []));

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
        $allTopics = Question::distinct()->pluck('topic')->toArray();
        $show_question = $this->show_question;
        return view('contents.admin.quiz.form', compact('quiz', 'show_question', 'allTopics'));
    }

    public function update(QuizRequest $request, Quiz $quiz)
    {
        $this->authorize('quiz.edit');

        $quiz->update($this->prepareQuizData($request));
        DB::table('quiz_topics')->where('quiz_id', $quiz->id)->delete();
        $this->syncQuizTopics($quiz, $request->input('topics', []));

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

    protected function prepareQuizData(QuizRequest $request): array
    {
        $data = $request->except('topics');

        $easy = $this->normalizeCount($request->input('easy_questions_count'));
        $medium = $this->normalizeCount($request->input('medium_questions_count'));
        $hard = $this->normalizeCount($request->input('hard_questions_count'));
        $difficultyTotal = $easy + $medium + $hard;

        $data['easy_questions_count'] = $easy;
        $data['medium_questions_count'] = $medium;
        $data['hard_questions_count'] = $hard;
        $data['random_question'] = $difficultyTotal > 0
            ? $difficultyTotal
            : $this->normalizeCount($request->input('random_question'));

        return $data;
    }

    protected function normalizeCount($value): int
    {
        return max(0, (int) $value);
    }

    protected function syncQuizTopics(Quiz $quiz, array $topics): void
    {
        if (empty($topics)) {
            return;
        }

        foreach ($topics as $topic) {
            DB::table('quiz_topics')->insert([
                'quiz_id' => $quiz->id,
                'topic' => $topic,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
