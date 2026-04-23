<?php

namespace App\Http\Controllers\learn;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Sessionable;
use App\Models\Workout;
use App\Models\WorkoutRestartLog;
use App\Utility\Modules\Tasks\TaskFactory;
use App\Utility\Question\QuestionFactory;
use App\Utility\Workout\WorkoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    public function restart(Request $request, Workout $workout)
    {
        $usedDDA = $request->input('used_dda', 1);
        $difficulty = $request->input('dda_difficulty');

        // Update workout dengan mode yang akan digunakan
        $workout->update([
            'used_dda' => (bool)$usedDDA,
            'current_dda_difficulty' => $difficulty,
            'is_completed' => 0,
            'is_mentor' => 0,
            'score' => 0,
            'date_get_score' => null,
        ]);

        try { $workout->WorkOutQuiz()->delete(); } catch (\Throwable $e) { }

        $sessionable = $workout->Sessionable;
        if ($sessionable && method_exists(WorkoutService::class, 'setWorkOutQuizSyncForThisExcersice')) {
            if ($usedDDA && $difficulty) {
                WorkoutService::setWorkOutQuizSyncForThisExcersice($workout, $sessionable->Model, $difficulty);
            } else {
                WorkoutService::setWorkOutQuizSyncForThisExcersice($workout, $sessionable->Model);
            }
        }

        $participantId = $workout->participant_id ?? optional($workout->Participant)->id;
        $sessionableId = $workout->sessionable_id ?? optional($workout->Sessionable)->id;
        if ($participantId && $sessionableId) {
            return redirect()->route('taskLearner', ['participant' => $participantId, 'sessionable' => $sessionableId]);
        }
        return redirect()->back();
    }

    public function prepared(Participant $participant, Sessionable $sessionable)
    {
        WorkoutService::WorkOutSyncForThisExcersice($participant, $sessionable, Auth::user());
        return redirect(route('taskLearner', ['participant' => $participant, 'sessionable' => $sessionable]));
    }

    public function task(Participant $participant, Sessionable $sessionable)
    {
        $className = $sessionable->sessionable_type;
        $task = TaskFactory::Build($className);
        $task->set_user(Auth::user());
        return $task->Render($participant, $sessionable);
    }

        public function completedAndNext(Workout $workout, Request $request)
    {
        $hasLogs = $workout->WorkOutQuiz && $workout->WorkOutQuiz->count() > 0;
        if ($hasLogs) {
            WorkoutService::recomputeScore($workout);
        } else {
            $workout->update(['is_completed' => 1, 'score' => 100, 'date_get_score' => now()]);
        }

        // Simpan hasil pengerjaan saat ini ke history SEBELUM di-reset untuk pengerjaan berikutnya
        try {
            $sessionLogs = [];
            try {
                $sessionLogs = $workout->WorkOutQuiz()->get()->toArray();
            } catch (\Throwable $e) { }

            // Deteksi apakah ini pengerjaan PERTAMA KALI
            $isFirstAttempt = $workout->RestartLogs()->count() === 0;

            $usedDDA = $workout->used_dda ?? true;
            // Override: pengerjaan pertama HARUS NON-DDA
            if ($isFirstAttempt) {
                $usedDDA = false;
            }

            $score = $workout->score ?? 0;

// CALCULATE TOPIC LEVELS BASED ON ACTUAL PERFORMANCE
$topicLevels = [];
try {
    if ($hasLogs) {
        // Fetch all quiz logs with their related questions
        $quizLogs = $workout->WorkOutQuiz()
            ->with('Question')
            ->get();

        if ($quizLogs->count() > 0) {
            // Group logs by topic and calculate accuracy per topic
            $topicStats = [];

            foreach ($quizLogs as $log) {
                $question = $log->Question;
                if ($question && $question->topic) {
                    $topic = $question->topic;

                    if (!isset($topicStats[$topic])) {
                        $topicStats[$topic] = [
                            'total_score' => 0,
                            'total_count' => 0,
                        ];
                    }

                    // Accumulate scores (assuming score is 0-100 or can be converted)
                    $questionScore = (int) ($log->score ?? 0);
                    $topicStats[$topic]['total_score'] += $questionScore;
                    $topicStats[$topic]['total_count'] += 1;
                }
            }

            // Convert accuracy to level (0-4 scale)
            foreach ($topicStats as $topic => $stats) {
                if ($stats['total_count'] > 0) {
                    $accuracy = ($stats['total_score'] / ($stats['total_count'] * 100)) * 100;

                    // Map accuracy percentage to level
                    if ($accuracy >= 80) {
                        $topicLevels[$topic] = 4;
                    } elseif ($accuracy >= 60) {
                        $topicLevels[$topic] = 3;
                    } elseif ($accuracy >= 40) {
                        $topicLevels[$topic] = 2;
                    } elseif ($accuracy > 0) {
                        $topicLevels[$topic] = 1;
                    } else {
                        $topicLevels[$topic] = 0;
                    }
                }
            }
        }
    } else {
        // No logs, initialize with defaults from quiz topics
        if ($workout->Sessionable) {
            $quizModel = $workout->Sessionable->Model;
            if ($quizModel && method_exists($quizModel, 'getTopicsArray')) {
                $topics = $quizModel->getTopicsArray();
                foreach ($topics as $topic) {
                    $topicLevels[$topic] = 0;
                }
            }
        }
    }
} catch (\Throwable $e) { }

            // Tentukan difficulty berdasarkan mode
            $ddaDifficulty = null;
            $nonDdaDifficulty = null;

            if ($usedDDA) {
                $ddaDifficulty = $workout->current_dda_difficulty;
            } else {
                // Deteksi difficulty dari soal yang dikerjakan
                if (!empty($sessionLogs)) {
                    $difficulties = collect($sessionLogs)->pluck('difficulty')->filter()->unique()->toArray();
                    $nonDdaDifficulty = !empty($difficulties) ? implode(',', $difficulties) : null;
                }
            }

            if (class_exists(WorkoutRestartLog::class)) {
                WorkoutRestartLog::create([
                    'workout_id' => $workout->id,
                    'user_id' => Auth::id(),
                    'previous_score' => $score,
                    'dda_difficulty' => $ddaDifficulty,
                    'non_dda_difficulty' => $nonDdaDifficulty,
                    'topic_levels' => !empty($topicLevels) ? json_encode($topicLevels) : null,
                    'payload' => $sessionLogs,
                    'used_dda' => (bool)$usedDDA,
                ]);
            }
        } catch (\Throwable $e) { }

        // Reset untuk pengerjaan berikutnya - GUNAKAN DDA UNTUK ATTEMPT BERIKUTNYA
        $workout->update([
            'used_dda' => 1, // default ke DDA untuk pengerjaan berikutnya
            'current_dda_difficulty' => null,
        ]);

        $participantId = $workout->participant_id ?? optional($workout->Participant)->id;
        $currentSessionable = $workout->Sessionable;
        $sessionId = optional($workout->Session)->id ?? optional($currentSessionable)->session_id;

        // stay on review if requested
        if ($request->query('stay') === 'review' && $participantId && $currentSessionable) {
            return redirect()->route('taskLearner', ['participant' => $participantId, 'sessionable' => $currentSessionable->id]);
        }

        if ($sessionId && $currentSessionable) {
            $currentOrder = $currentSessionable->order ?? null;
            $next = Sessionable::where('session_id', $sessionId)
                ->when($currentOrder !== null, function ($q) use ($currentOrder) { $q->where('order', '>', $currentOrder); })
                ->orderBy('order')
                ->first();
            if ($next && $participantId) {
                return redirect()->route('taskLearner', ['participant' => $participantId, 'sessionable' => $next->id]);
            }
        }
        if ($participantId && $currentSessionable) {
            return redirect()->route('taskLearner', ['participant' => $participantId, 'sessionable' => $currentSessionable->id]);
        }
        return redirect()->back();
    }


    public function workout(Request $request)
    {
        $request->validate(['question_id' => 'required|int','workout_id' => 'required|int']);
        $question = Question::findorfail($request->question_id);
        $workout = Workout::findorfail($request->workout_id);
        $result =  QuestionFactory::Build($question->questionType)->workoutChecker($question, $workout, $request);
        return response()->json($result);
    }
}
