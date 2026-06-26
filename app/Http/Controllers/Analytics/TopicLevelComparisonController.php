<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Sessionable;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopicLevelComparisonController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $this->isAdminUser($user);

        $terms = $isAdmin
            ? Term::all()
            : Term::whereIn('id', DB::table('term_user')->where('user_id', $user->id)->pluck('term_id'))->get();

        return view('contents.admin.analytics.topic-levels-comparison', [
            'terms' => $terms,
        ]);
    }

    public function getTermParticipants(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
        ]);

        try {
            $termId = $validated['term_id'];
            $user = Auth::user();

            if (!$this->canAccessTerm($user, $termId)) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            return response()->json([
                'success' => true,
                'students' => $this->studentsForTerm($termId),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getComparisonData(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'nullable|exists:sessions,id',
            'sessionable_id' => 'nullable|exists:sessionables,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:participants,id',
        ]);

        try {
            $termId = $validated['term_id'];
            $sessionId = $validated['session_id'] ?? null;
            $sessionableId = $validated['sessionable_id'] ?? null;
            $studentIds = $validated['student_ids'];

            $user = Auth::user();
            if (!$this->canAccessTerm($user, $termId)) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $participants = Participant::with(['User', 'Workout.RestartLogs', 'Workout.WorkOutQuiz.Question'])
                ->whereIn('id', $studentIds)
                ->where('term_id', $termId)
                ->get();

            $allTopics = collect();
            $ddaData = [];
            $nonddaData = [];
            $overallData = [];
            $students = [];

            foreach ($participants as $participant) {
                $ddaLevels = $this->aggregateTopicLevelsByMode($participant, $sessionId, $sessionableId, true);
                $nonDdaLevels = $this->aggregateTopicLevelsByMode($participant, $sessionId, $sessionableId, false);
                $overallLevels = $this->aggregateTopicLevelsByMode($participant, $sessionId, $sessionableId, null);

                $allTopics = $allTopics
                    ->merge(array_keys($ddaLevels))
                    ->merge(array_keys($nonDdaLevels))
                    ->merge(array_keys($overallLevels));

                $studentInfo = [
                    'id' => $participant->id,
                    'name' => $participant->User->name,
                    'user_id' => $participant->user_id,
                    'email' => $participant->User->email,
                ];

                $students[] = $studentInfo;
                $ddaData[] = array_merge($studentInfo, ['levels' => $ddaLevels]);
                $nonddaData[] = array_merge($studentInfo, ['levels' => $nonDdaLevels]);
                $overallData[] = array_merge($studentInfo, ['levels' => $overallLevels]);
            }

            $topics = $allTopics->unique()->sort()->values()->toArray();

            return response()->json([
                'success' => true,
                'students' => $students,
                'topics' => $topics,
                'dda_data' => $ddaData,
                'nondda_data' => $nonddaData,
                'overall_data' => $overallData,
                'stats' => [
                    'dda_count' => count($ddaData),
                    'nondda_count' => count($nonddaData),
                    'overall' => $this->calculateStats($topics, $overallData),
                    'dda_avg' => $this->averageLevels($topics, $ddaData),
                    'nondda_avg' => $this->averageLevels($topics, $nonddaData),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getAvailableQuizzes(Request $request)
    {
        $termId = $request->query('term_id');
        if (!$termId) {
            return response()->json([], 400);
        }

        $user = Auth::user();
        if (!$this->canAccessTerm($user, $termId)) {
            return response()->json([], 403);
        }

        try {
            $quizzes = Sessionable::whereHas('session', fn($q) => $q->where('term_id', $termId))
                ->where('type', 'workout')
                ->select('id', 'title')
                ->get()
                ->toArray();

            return response()->json($quizzes);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getQuizComparison(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'sessionable_id' => 'required|exists:sessionables,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:participants,id',
        ]);

        $termId = $validated['term_id'];
        $sessionableId = $validated['sessionable_id'];
        $studentIds = $validated['student_ids'];

        $user = Auth::user();
        if (!$this->canAccessTerm($user, $termId)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $sessionable = Sessionable::find($sessionableId);
        if (!$sessionable) {
            return response()->json(['success' => false, 'error' => 'Quiz not found'], 404);
        }

        $participants = Participant::with(['User', 'Workout.RestartLogs', 'Workout.WorkOutQuiz.Question'])
            ->whereIn('id', $studentIds)
            ->where('term_id', $termId)
            ->get();

        $topics = collect();
        $quizData = [];
        $overallData = [];

        foreach ($participants as $participant) {
            $quizLevels = $this->aggregateTopicLevelsBySessionable($participant, $sessionableId, null);
            $overallLevels = $this->aggregateTopicLevelsByMode($participant, null, null, null);

            $topics = $topics->merge(array_keys($quizLevels))->merge(array_keys($overallLevels));

            $studentInfo = [
                'id' => $participant->id,
                'name' => $participant->User->name,
            ];

            $quizData[] = array_merge($studentInfo, ['levels' => $quizLevels]);
            $overallData[] = array_merge($studentInfo, ['levels' => $overallLevels]);
        }

        $topics = $topics->unique()->sort()->values()->toArray();

        return response()->json([
            'success' => true,
            'quiz_name' => $sessionable->title,
            'topics' => $topics,
            'quiz_data' => $quizData,
            'overall_data' => $overallData,
            'stats' => [
                'quiz' => $this->calculateStats($topics, $quizData),
                'overall' => $this->calculateStats($topics, $overallData),
            ],
        ]);
    }

    public function getDDAMetrics(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:participants,id',
        ]);

        $termId = $validated['term_id'];
        $studentIds = $validated['student_ids'];

        $user = Auth::user();
        if (!$this->canAccessTerm($user, $termId)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $participants = Participant::with('User')
            ->whereIn('id', $studentIds)
            ->where('term_id', $termId)
            ->get();

        $difficultyStats = ['easy' => 0, 'medium' => 0, 'hard' => 0];
        $successByDifficulty = [
            'easy' => ['success' => 0, 'total' => 0],
            'medium' => ['success' => 0, 'total' => 0],
            'hard' => ['success' => 0, 'total' => 0],
        ];
        $totalAttempts = 0;

        foreach ($participants as $participant) {
            $logs = $participant->Workout()->with('RestartLogs')->get()->pluck('RestartLogs')->flatten();
            foreach ($logs as $log) {
                if (!(bool) $log->used_dda) {
                    continue;
                }

                $difficulty = $log->dda_difficulty ?: 'medium';
                if (isset($difficultyStats[$difficulty])) {
                    $difficultyStats[$difficulty]++;
                    $totalAttempts++;
                    $successByDifficulty[$difficulty]['total']++;
                    if ((float) ($log->previous_score ?? 0) >= 70) {
                        $successByDifficulty[$difficulty]['success']++;
                    }
                }
            }
        }

        $distribution = [];
        foreach ($difficultyStats as $difficulty => $count) {
            $distribution[$difficulty] = [
                'count' => $count,
                'percentage' => $totalAttempts > 0 ? round(($count / $totalAttempts) * 100, 2) : 0,
            ];
        }

        $successRates = [];
        foreach ($successByDifficulty as $difficulty => $stats) {
            $successRates[$difficulty] = [
                'total' => $stats['total'],
                'success_count' => $stats['success'],
                'success_rate' => $stats['total'] > 0 ? round(($stats['success'] / $stats['total']) * 100, 2) : 0,
            ];
        }

        $effectiveCount = array_sum(array_column($successByDifficulty, 'success'));
        $effectiveness = $totalAttempts > 0 ? round(($effectiveCount / $totalAttempts) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'total_dda_attempts' => $totalAttempts,
            'difficulty_distribution' => $distribution,
            'success_rates' => $successRates,
            'overall_effectiveness' => $effectiveness,
            'recommendation' => $effectiveness >= 70
                ? 'DDA efektif. Lanjutkan penggunaan untuk adaptive learning.'
                : 'DDA perlu evaluasi dan penyesuaian strategi.',
        ]);
    }

    public function generateSPKRecommendations(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:participants,id',
        ]);

        $termId = $validated['term_id'];
        $studentIds = $validated['student_ids'];

        $user = Auth::user();
        if (!$this->canAccessTerm($user, $termId)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $participants = Participant::with(['User'])
            ->whereIn('id', $studentIds)
            ->where('term_id', $termId)
            ->get();

        $recommendations = [];
        foreach ($participants as $participant) {
            $logs = $participant->Workout()->with('RestartLogs')->get()->pluck('RestartLogs')->flatten();
            $ddaScore = 0;
            $ddaCount = 0;
            $nonDdaScore = 0;
            $nonDdaCount = 0;
            $weakTopics = [];

            foreach ($logs as $log) {
                $score = (float) ($log->previous_score ?? 0);
                if ((bool) $log->used_dda) {
                    $ddaScore += $score;
                    $ddaCount++;
                } else {
                    $nonDdaScore += $score;
                    $nonDdaCount++;
                }

                $topics = is_string($log->topic_levels) ? json_decode($log->topic_levels, true) : $log->topic_levels;
                if (is_array($topics)) {
                    foreach ($topics as $topic => $level) {
                        if ((int) $level < 2) {
                            $weakTopics[$topic] = true;
                        }
                    }
                }
            }

            $avgDda = $ddaCount > 0 ? round($ddaScore / $ddaCount, 2) : 0;
            $avgNonDda = $nonDdaCount > 0 ? round($nonDdaScore / $nonDdaCount, 2) : 0;
            $level = count($weakTopics) >= 3 ? 'high' : (count($weakTopics) >= 1 ? 'medium' : 'low');

            $recommendations[] = [
                'student_id' => $participant->id,
                'student_name' => $participant->User->name,
                'student_email' => $participant->User->email,
                'dda_attempts' => $ddaCount,
                'non_dda_attempts' => $nonDdaCount,
                'avg_score_dda' => $avgDda,
                'avg_score_non_dda' => $avgNonDda,
                'dda_improvement' => round($avgDda - $avgNonDda, 2),
                'weak_topics' => array_map(fn($topic) => ['topic' => $topic], array_keys($weakTopics)),
                'strong_topics' => [],
                'recommendation' => $level === 'high'
                    ? 'Membutuhkan intervensi tinggi.'
                    : ($level === 'medium' ? 'Membutuhkan monitoring dan support.' : 'Performa baik. Lanjutkan pembelajaran.'),
                'intervention_level' => $level,
                'suggested_strategy' => $level === 'high'
                    ? 'Konsultasi one-on-one dan latihan topik lemah.'
                    : ($level === 'medium' ? 'Latihan tambahan dan pendampingan.' : 'Pertahankan pola belajar saat ini.'),
            ];
        }

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'export_timestamp' => now()->format('Y-m-d H:i:s'),
            'term_id' => $termId,
        ]);
    }

    private function studentsForTerm(int $termId): array
    {
        $participants = Participant::where('term_id', $termId)->with('User')->get();
        $students = [];

        foreach ($participants as $participant) {
            $user = $participant->User;
            if (!$user || !$user->getRoleNames()->contains('student')) {
                continue;
            }

            $students[] = [
                'id' => $participant->id,
                'user_id' => $participant->user_id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        return $students;
    }

        private function canAccessTerm(User $user, $termId): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        return DB::table('term_user')->where('user_id', $user->id)->where('term_id', $termId)->exists();
    }

    private function aggregateTopicLevelsByMode(Participant $participant, $sessionId = null, $sessionableId = null, $useDda = null): array
    {
        $topicLevels = [];
        $workouts = $participant->Workout()
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->when($sessionableId, fn($q) => $q->where('sessionable_id', $sessionableId))
            ->where('is_completed', 1)
            ->with('RestartLogs')
            ->get();

        foreach ($workouts as $workout) {
            $logs = $workout->RestartLogs;
            if ($useDda !== null) {
                $logs = $logs->filter(fn($log) => (bool) $log->used_dda === (bool) $useDda);
            }

            foreach ($logs as $log) {
                $levels = is_string($log->topic_levels) ? json_decode($log->topic_levels, true) : $log->topic_levels;
                if (!is_array($levels)) {
                    continue;
                }

                foreach ($levels as $topic => $level) {
                    $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, (int) $level);
                }
            }
        }

        return $topicLevels;
    }

    private function aggregateTopicLevelsBySessionable(Participant $participant, $sessionableId, $useDda = null): array
    {
        $topicLevels = [];
        $workouts = $participant->Workout()
            ->where('sessionable_id', $sessionableId)
            ->where('is_completed', 1)
            ->with('RestartLogs')
            ->get();

        foreach ($workouts as $workout) {
            $logs = $workout->RestartLogs;
            if ($useDda !== null) {
                $logs = $logs->filter(fn($log) => (bool) $log->used_dda === (bool) $useDda);
            }

            foreach ($logs as $log) {
                $levels = is_string($log->topic_levels) ? json_decode($log->topic_levels, true) : $log->topic_levels;
                if (!is_array($levels)) {
                    continue;
                }

                foreach ($levels as $topic => $level) {
                    $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, (int) $level);
                }
            }
        }

        return $topicLevels;
    }

    private function calculateStats(array $topics, array $data): array
    {
        return [
            'total_students' => count($data),
            'total_topics' => count($topics),
            'avg_level_by_topic' => $this->averageLevels($topics, $data),
        ];
    }
    private function isAdminUser(User $user): bool
    {
        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('model_has_roles.model_id', $user->id)
            ->whereIn(DB::raw('LOWER(roles.name)'), ['admin', 'super-admin'])
            ->exists();
    }
    private function averageLevels(array $topics, array $data): array
    {
        $avgByTopic = [];

        foreach ($topics as $topic) {
            $levels = collect($data)
                ->pluck('levels')
                ->map(fn($levels) => $levels[$topic] ?? null)
                ->filter(fn($value) => $value !== null);

            if ($levels->count() > 0) {
                $avgByTopic[$topic] = round($levels->avg(), 2);
            }
        }

        return $avgByTopic;
    }
}
