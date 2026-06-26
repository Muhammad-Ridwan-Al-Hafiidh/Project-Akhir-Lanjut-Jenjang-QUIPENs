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

class SPKRecommendationController extends Controller
{
    public function index()
    {
        $topics = Question::select('topic')
            ->whereNotNull('topic')
            ->distinct()
            ->pluck('topic')
            ->sort()
            ->values();

        return view('contents.admin.analytics.spk-recommendation', ['topics' => $topics]);
    }

    public function getRecommendations(Request $request)
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
            $recommendations = [];

            foreach ($participants as $pid => $pWorkouts) {
                $name = $userMap[$pid] ?? 'Unknown';
                $studentList[] = ['id' => (int) $pid, 'name' => $name];

                $ddaScore = 0;
                $ddaCount = 0;
                $nonDdaScore = 0;
                $nonDdaCount = 0;
                $allTopicLevels = [];

                foreach ($pWorkouts as $workout) {
                    foreach ($workout->RestartLogs as $log) {
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
                                $intLevel = (int) $level;
                                $allTopicLevels[$topic] = max($allTopicLevels[$topic] ?? 0, $intLevel);
                            }
                        }
                    }
                }

                $weakTopics = [];
                $strongTopics = [];
                foreach ($allTopicLevels as $topic => $lv) {
                    if ($lv <= 2) $weakTopics[] = $topic;
                    if ($lv >= 3) $strongTopics[] = $topic;
                }

                $avgDda = $ddaCount > 0 ? round($ddaScore / $ddaCount, 2) : 0;
                $avgNonDda = $nonDdaCount > 0 ? round($nonDdaScore / $nonDdaCount, 2) : 0;
                $improvement = $avgDda - $avgNonDda;
                $weakCount = count($weakTopics);
                $level = $weakCount >= 3 ? 'high' : ($weakCount >= 1 ? 'medium' : 'low');

                $recommendations[] = [
                    'student_id' => (int) $pid,
                    'student_name' => $name,
                    'dda_attempts' => $ddaCount,
                    'non_dda_attempts' => $nonDdaCount,
                    'avg_score_dda' => $avgDda,
                    'avg_score_non_dda' => $avgNonDda,
                    'dda_improvement' => round($improvement, 2),
                    'weak_topics' => $weakTopics,
                    'strong_topics' => $strongTopics,
                    'all_topic_levels' => $allTopicLevels,
                    'intervention_level' => $level,
                    'recommendation' => $level === 'high'
                        ? 'Membutuhkan intervensi tinggi.'
                        : ($level === 'medium' ? 'Membutuhkan monitoring dan support.' : 'Performa baik. Lanjutkan pembelajaran.'),
                ];
            }

            return response()->json([
                'success' => true,
                'students' => $studentList,
                'recommendations' => $recommendations,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getStudentReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|integer',
            ]);

            $participantId = (int) $validated['student_id'];

            $tu = DB::table('term_user')->where('id', $participantId)->first();
            if (!$tu) {
                return response()->json(['success' => false, 'error' => 'Data mahasiswa tidak ditemukan.']);
            }

            $userId = $tu->user_id;

            $enrollments = DB::table('term_user')
                ->where('user_id', $userId)
                ->where('role_id', 4)
                ->pluck('id');

            $terms = DB::table('term_user')
                ->join('terms', 'terms.id', '=', 'term_user.term_id')
                ->whereIn('term_user.id', $enrollments)
                ->select('term_user.id as term_user_id', 'terms.id as term_id', 'terms.title as course_name')
                ->get();

            $report = [];

            foreach ($terms as $term) {
                $workoutIds = DB::table('workouts')
                    ->where('participant_id', $term->term_user_id)
                    ->pluck('id');

                if ($workoutIds->isEmpty()) continue;

                $avgScore = DB::table('workout_quiz_logs')
                    ->whereIn('workout_id', $workoutIds)
                    ->avg('score');

                $avgScore = $avgScore ? round($avgScore, 2) : 0;

                $restartLogs = WorkoutRestartLog::whereIn('workout_id', $workoutIds)->get();

                $allTopicLevels = [];
                foreach ($restartLogs as $log) {
                    $topics = is_string($log->topic_levels) ? json_decode($log->topic_levels, true) : $log->topic_levels;
                    if (is_array($topics)) {
                        foreach ($topics as $topic => $level) {
                            $allTopicLevels[$topic] = max($allTopicLevels[$topic] ?? 0, (int) $level);
                        }
                    }
                }

                $weakest = null;
                $weakestLevel = 999;
                $strongest = null;
                $strongestLevel = -1;

                foreach ($allTopicLevels as $topic => $lv) {
                    if ($lv < $weakestLevel) {
                        $weakestLevel = $lv;
                        $weakest = $topic;
                    }
                    if ($lv > $strongestLevel) {
                        $strongestLevel = $lv;
                        $strongest = $topic;
                    }
                }

                $gradeLetter = $this->scoreToGrade($avgScore);
                $gradePoint = $this->gradeToPoint($gradeLetter);

                $report[] = [
                    'course_name' => $term->course_name,
                    'avg_score' => $avgScore,
                    'grade' => $gradeLetter,
                    'grade_point' => $gradePoint,
                    'strongest_topic' => $strongest,
                    'strongest_level' => $strongestLevel >= 0 ? $strongestLevel : null,
                    'weakest_topic' => $weakest,
                    'weakest_level' => $weakestLevel < 999 ? $weakestLevel : null,
                    'all_topic_levels' => $allTopicLevels,
                ];
            }

            $totalPoints = array_sum(array_column($report, 'grade_point'));
            $totalCourses = count($report);
            $ipk = $totalCourses > 0 ? round($totalPoints / $totalCourses, 2) : 0;

            $user = User::find($userId);

            return response()->json([
                'success' => true,
                'student_name' => $user ? $user->name : 'Unknown',
                'ipk' => $ipk,
                'total_courses' => $totalCourses,
                'report' => $report,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function scoreToGrade($score): string
    {
        if ($score >= 80) return 'A';
        if ($score >= 75) return 'A-';
        if ($score >= 70) return 'B+';
        if ($score >= 65) return 'B';
        if ($score >= 60) return 'B-';
        if ($score >= 55) return 'C+';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        return 'E';
    }

    private function gradeToPoint($grade): float
    {
        return match ($grade) {
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            default => 0.00,
        };
    }
}