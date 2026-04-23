<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Term;
use App\Models\Participant;
use App\Models\Session;
use App\Models\Sessionable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicLevelComparisonController extends Controller
{
    /**
     * Display topic levels comparison page
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $terms = Term::all();
        } else {
            $terms = $user->Terms()->get();
        }

        return view('contents.admin.analytics.topic-levels-comparison', [
            'terms' => $terms,
        ]);
    }

    /**
     * Fetch comparison data with DDA vs Non-DDA separation
     */
    public function getComparisonData(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'nullable|exists:sessions,id',
            'sessionable_id' => 'nullable|exists:sessionables,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:term_user,id',
        ]);

        try {
            $termId = $validated['term_id'];
            $sessionId = $validated['session_id'] ?? null;
            $sessionableId = $validated['sessionable_id'] ?? null;
            $studentIds = $validated['student_ids'];

            // Verify access
            $user = Auth::user();
            if (!$user->hasRole('admin') && !$user->Terms()->where('terms.id', $termId)->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get participants dengan eager loading
            $participants = Participant::with(['User', 'Workout.RestartLogs', 'Workout.WorkOutQuiz.Question'])
                ->whereIn('id', $studentIds)
                ->where('term_id', $termId)
                ->get();

            // Build comparison data
            $allTopics = collect();
            $ddaData = [];
            $nondDaData = [];
            $overallData = [];
            $students = [];

            foreach ($participants as $participant) {
                // Aggregate DDA data
                $ddaLevels = $this->aggregateTopicLevelsByMode(
                    $participant,
                    $sessionId,
                    $sessionableId,
                    true // DDA mode
                );

                // Aggregate Non-DDA data
                $nondDaLevels = $this->aggregateTopicLevelsByMode(
                    $participant,
                    $sessionId,
                    $sessionableId,
                    false // Non-DDA mode
                );

                // Aggregate overall data (all attempts)
                $overallLevels = $this->aggregateTopicLevelsByMode(
                    $participant,
                    $sessionId,
                    $sessionableId,
                    null // All modes
                );

                // Collect all topics
                $allTopics = $allTopics
                    ->merge(array_keys($ddaLevels))
                    ->merge(array_keys($nondDaLevels))
                    ->merge(array_keys($overallLevels));

                $studentInfo = [
                    'id' => $participant->id,
                    'name' => $participant->User->name,
                    'user_id' => $participant->user_id,
                    'email' => $participant->User->email,
                ];

                $students[] = $studentInfo;

                $ddaData[] = array_merge($studentInfo, [
                    'levels' => $ddaLevels,
                ]);

                $nondDaData[] = array_merge($studentInfo, [
                    'levels' => $nondDaLevels,
                ]);

                $overallData[] = array_merge($studentInfo, [
                    'levels' => $overallLevels,
                ]);
            }

            // Get unique topics sorted
            $topics = $allTopics->unique()->sort()->values()->toArray();

            // Calculate statistics per mode
            $stats = [
                'dda' => $this->calculateStats($topics, $ddaData),
                'nondda' => $this->calculateStats($topics, $nondDaData),
                'overall' => $this->calculateStats($topics, $overallData),
            ];

            return response()->json([
                'success' => true,
                'students' => $students,
                'topics' => $topics,
                'dda_data' => $ddaData,
                'nondda_data' => $nondDaData,
                'overall_data' => $overallData,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aggregate topic levels for a participant by mode
     *
     * @param Participant $participant
     * @param int|null $sessionId
     * @param int|null $sessionableId
     * @param bool|null $useDda - true=DDA only, false=Non-DDA only, null=all
     * @return array Topic levels keyed by topic name
     */
    private function aggregateTopicLevelsByMode(Participant $participant, $sessionId = null, $sessionableId = null, $useDda = null)
    {
        $topicLevels = [];

        // Get all workouts for this participant
        $workouts = $participant->Workout()
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->when($sessionableId, fn($q) => $q->where('sessionable_id', $sessionableId))
            ->where('is_completed', 1)
            ->with('RestartLogs')
            ->get();

        foreach ($workouts as $workout) {
            // Filter RestartLogs by mode if specified
            $logs = $workout->RestartLogs;

            if ($useDda !== null) {
                $logs = $logs->filter(fn($log) => $log->used_dda == $useDda);
            }

            // Process each log's topic levels
            foreach ($logs as $log) {
                if ($log->topic_levels) {
                    $logTopics = is_string($log->topic_levels)
                        ? json_decode($log->topic_levels, true)
                        : $log->topic_levels;

                    if (is_array($logTopics)) {
                        foreach ($logTopics as $topic => $level) {
                            $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, (int)$level);
                        }
                    }
                }
            }

            // Also aggregate from WorkoutQuizLogs for comprehensive accuracy
            $quizLogs = $workout->WorkOutQuiz()
                ->with('Question')
                ->get();

            $topicStats = [];
            foreach ($quizLogs as $log) {
                if ($log->Question && $log->Question->topic) {
                    $topic = $log->Question->topic;
                    if (!isset($topicStats[$topic])) {
                        $topicStats[$topic] = [
                            'total_score' => 0,
                            'total_count' => 0,
                        ];
                    }
                    $topicStats[$topic]['total_score'] += (int)($log->score ?? 0);
                    $topicStats[$topic]['total_count'] += 1;
                }
            }

            // Convert accuracy to level
            foreach ($topicStats as $topic => $stats) {
                if ($stats['total_count'] > 0) {
                    $accuracy = ($stats['total_score'] / ($stats['total_count'] * 100)) * 100;

                    $level = match(true) {
                        $accuracy >= 80 => 4,
                        $accuracy >= 60 => 3,
                        $accuracy >= 40 => 2,
                        $accuracy > 0 => 1,
                        default => 0
                    };

                    // Keep max level
                    $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, $level);
                }
            }
        }

        return $topicLevels;
    }

    /**
     * Calculate comparison statistics
     */
    private function calculateStats($topics, $data)
    {
        $avgByTopic = [];
        $maxByTopic = [];
        $minByTopic = [];

        foreach ($topics as $topic) {
            $levels = collect($data)
                ->pluck('levels')
                ->map(fn($levels) => $levels[$topic] ?? 0)
                ->filter();

            if ($levels->count() > 0) {
                $avgByTopic[$topic] = round($levels->avg(), 2);
                $maxByTopic[$topic] = $levels->max();
                $minByTopic[$topic] = $levels->min();
            }
        }

        return [
            'total_students' => count($data),
            'total_topics' => count($topics),
            'avg_level_by_topic' => $avgByTopic,
            'max_level_by_topic' => $maxByTopic,
            'min_level_by_topic' => $minByTopic,
        ];
    }
}
