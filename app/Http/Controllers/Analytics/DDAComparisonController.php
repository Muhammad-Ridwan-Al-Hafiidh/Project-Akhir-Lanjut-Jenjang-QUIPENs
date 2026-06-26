<?php
namespace App\Http\Controllers\Analytics;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Course;
use App\Models\Question;
use App\Models\Workout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DDAComparisonController extends Controller
{
    public function index()
    {
        $topics = Question::select('topic')
            ->whereNotNull('topic')
            ->distinct()
            ->pluck('topic')
            ->sort()
            ->values();

        return view('contents.admin.analytics.dda-comparison', ['topics' => $topics]);
    }

    public function getComparison(Request $request)
    {
        try {
            $validated = $request->validate([
                'topic_ids' => 'required|array|min:1',
                'topic_ids.*' => 'string',
            ]);

            $topicIds = $validated['topic_ids'];
            $questionIds = Question::whereIn('topic', $topicIds)->pluck('id');

            if ($questionIds->isEmpty()) {
                return response()->json(['success' => false, 'error' => 'Tidak ada soal untuk topik ini.']);
            }

            $quizRows = DB::table('workout_quiz_logs')
                ->whereIn('question_id', $questionIds)
                ->get();

            if ($quizRows->isEmpty()) {
                return response()->json(['success' => false, 'error' => 'Belum ada soal yang dijawab untuk topik ini.']);
            }

            $workoutIds = $quizRows->pluck('workout_id')->unique();

            $participantIds = DB::table('workouts')
                ->whereIn('id', $workoutIds)
                ->pluck('participant_id')
                ->unique();

            $userMap = DB::table('term_user')
                ->join('users', 'users.id', '=', 'term_user.user_id')
                ->whereIn('term_user.id', $participantIds)
                ->pluck('users.name', 'term_user.id');

            $workouts = Workout::whereIn('id', $workoutIds)
                ->with('RestartLogs')
                ->get();

            $participants = $workouts->groupBy('participant_id');
            $studentList = [];
            $allTopics = collect();
            $ddaData = [];
            $nonddaData = [];

            foreach ($participants as $pid => $pWorkouts) {
                $name = $userMap[$pid] ?? 'Unknown';
                $studentList[] = ['id' => (int) $pid, 'name' => $name];

                $ddaLevels = $this->aggregateTopicLevels($pWorkouts, true);
                $nonddaLevels = $this->aggregateTopicLevels($pWorkouts, false);

                $allTopics = $allTopics->merge(array_keys($ddaLevels))->merge(array_keys($nonddaLevels));
                $ddaData[] = ['id' => (int) $pid, 'name' => $name, 'levels' => $ddaLevels];
                $nonddaData[] = ['id' => (int) $pid, 'name' => $name, 'levels' => $nonddaLevels];
            }

            $topics = $allTopics->unique()->sort()->values()->toArray();

            return response()->json([
                'success' => true,
                'students' => $studentList,
                'topics' => $topics,
                'dda_data' => $ddaData,
                'nondda_data' => $nonddaData,
                'stats' => [
                    'dda_count' => count($ddaData),
                    'nondda_count' => count($nonddaData),
                    'dda_avg' => $this->averageLevels($topics, $ddaData),
                    'nondda_avg' => $this->averageLevels($topics, $nonddaData),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function aggregateTopicLevels($workouts, $useDda): array
    {
        $topicLevels = [];
        foreach ($workouts as $workout) {
            $logs = $workout->RestartLogs;
            if ($useDda !== null) {
                $logs = $logs->filter(fn($log) => (bool) $log->used_dda === (bool) $useDda);
            }
            foreach ($logs as $log) {
                $levels = is_string($log->topic_levels) ? json_decode($log->topic_levels, true) : $log->topic_levels;
                if (!is_array($levels)) continue;
                foreach ($levels as $topic => $level) {
                    $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, (int) $level);
                }
            }
        }
        return $topicLevels;
    }

    private function averageLevels(array $topics, array $data): array
    {
        $avgByTopic = [];
        foreach ($topics as $topic) {
            $levels = collect($data)->pluck('levels')->map(fn($levels) => $levels[$topic] ?? null)->filter(fn($value) => $value !== null);
            if ($levels->count() > 0) $avgByTopic[$topic] = round($levels->avg(), 2);
        }
        return $avgByTopic;
    }
}