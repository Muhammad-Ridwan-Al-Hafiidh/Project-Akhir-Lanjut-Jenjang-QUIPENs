<?php
namespace App\Http\Controllers\Analytics;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Course;
use App\Models\Question;
use App\Models\Workout;
use App\Models\WorkoutRestartLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DDAMetricsController extends Controller
{
    public function index()
    {
        $topics = Question::select('topic')
            ->whereNotNull('topic')
            ->distinct()
            ->pluck('topic')
            ->sort()
            ->values();

        return view('contents.admin.analytics.dda-metrics', ['topics' => $topics]);
    }

    public function getStudents(Request $request)
    {
        $validated = $request->validate(['course_id' => 'required|exists:courses,id']);
        $courseId = $validated['course_id'];
        $termIds = DB::table('terms')->where('course_id', $courseId)->pluck('id');

        $students = [];
        foreach ($termIds as $termId) {
            $participants = Participant::where('term_id', $termId)->with('User')->get();
            foreach ($participants as $p) {
                if ($p->User && $p->User->getRoleNames()->contains('student')) {
                    if (!collect($students)->where('id', $p->id)->count()) {
                        $students[] = ['id' => $p->id, 'name' => $p->User->name];
                    }
                }
            }
        }
        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getStudentsByTopic(Request $request)
    {
        $validated = $request->validate([
            'topic_ids' => 'required|array|min:1',
            'topic_ids.*' => 'string',
        ]);

        $questionIds = Question::whereIn('topic', $validated['topic_ids'])->pluck('id');

        $students = DB::table('workout_quiz_logs')
            ->join('workouts', 'workouts.id', '=', 'workout_quiz_logs.workout_id')
            ->join('term_user', 'term_user.id', '=', 'workouts.participant_id')
            ->join('users', 'users.id', '=', 'term_user.user_id')
            ->whereIn('workout_quiz_logs.question_id', $questionIds)
            ->select('workouts.participant_id as id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getMetrics(Request $request)
    {
        $validated = $request->validate([
            'topic_ids' => 'required|array|min:1',
            'topic_ids.*' => 'string',
        ]);

        $topicIds = $validated['topic_ids'];
        $questionIds = Question::whereIn('topic', $topicIds)->pluck('id');

        $questions = Question::whereIn('id', $questionIds)->get();
        $quizLogs = DB::table('workout_quiz_logs')
            ->whereIn('question_id', $questionIds)
            ->get();

        $questionStats = [];
        foreach ($questions as $question) {
            $logs = $quizLogs->where('question_id', $question->id);
            $total = $logs->count();
            $success = $logs->where('score', '>', 0)->count();
            $questionStats[] = [
                'question_body' => $question->question_body,
                'topic' => $question->topic,
                'difficulty' => $question->difficulty,
                'total_answers' => $total,
                'correct_count' => $success,
                'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            ];
        }

        $topicSummary = [];
        foreach ($topicIds as $topic) {
            $qIds = $questions->where('topic', $topic)->pluck('id');
            $logs = $quizLogs->whereIn('question_id', $qIds);
            $total = $logs->count();
            $success = $logs->where('score', '>', 0)->count();
            $topicSummary[] = [
                'topic' => $topic,
                'total_questions' => $questions->where('topic', $topic)->count(),
                'total_answers' => $total,
                'correct_count' => $success,
                'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
            ];
        }

        $workoutIds = $quizLogs->pluck('workout_id')->unique();
        $participantIds = DB::table('workouts')
            ->whereIn('id', $workoutIds)
            ->pluck('participant_id')
            ->unique();

        $userMap = DB::table('term_user')
            ->join('users', 'users.id', '=', 'term_user.user_id')
            ->whereIn('term_user.id', $participantIds)
            ->pluck('users.name', 'term_user.id');

        $restartLogs = WorkoutRestartLog::whereIn('workout_id', $workoutIds)
            ->with('workout')
            ->orderBy('created_at', 'desc')
            ->get();

        $restartResults = [];
        foreach ($restartLogs as $log) {
            $pid = $log->workout->participant_id ?? null;
            $restartResults[] = [
                'student_name' => $pid ? ($userMap[$pid] ?? 'Unknown') : 'Unknown',
                'score' => (float) ($log->previous_score ?? 0),
                'used_dda' => (bool) $log->used_dda,
                'difficulty' => $log->used_dda ? ($log->dda_difficulty ?? 'N/A') : ($log->non_dda_difficulty ?? 'N/A'),
                'topic_levels' => $log->topic_levels,
                'created_at' => $log->created_at->format('Y-m-d H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'topic_summary' => $topicSummary,
            'question_stats' => $questionStats,
            'restart_results' => $restartResults,
        ]);
    }
}