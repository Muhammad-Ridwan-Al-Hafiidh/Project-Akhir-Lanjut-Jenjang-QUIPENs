<?php

namespace App\Utility\Workout;

use App\Models\Participant;
use App\Models\Quiz;
use App\Models\Sessionable;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutQuizLog;
use App\Models\Question;

abstract class WorkoutService
{
    public static function checkExistWorkout(int $participant_id, Sessionable $sessionable, User $user): ?Workout
    {
        return Workout::where('participant_id', $participant_id)
            ->where('sessionable_id', $sessionable->id)
            ->first();
    }

    public static function WorkOutSyncForThisExcersice(Participant $participant, Sessionable $sessionable, User $user): Workout
    {
        $workout = self::checkExistWorkout($participant->id, $sessionable, $user);
        if (empty($workout)) {
            $workout = new Workout();
            $workout->participant_id = $participant->id;
            $workout->sessionable_id = $sessionable->id;
            $workout->date_first_view = now();
            $workout->is_completed = 0;
            $workout->score = 0;
            $workout->save();
        }
        return $workout;
    }

    public static function setWorkOutQuizSyncForThisExcersice(Workout $workout, Quiz $quiz): ?array
    {
        if ($workout->WorkOutQuiz->count() > 0) {
            return [];
        }

        $n = (int) ($quiz->random_question ?? 0);

        if ($n > 0) {
            // Prefer global bank when random_question is set
            $questions = Question::inRandomOrder()->limit($n)->get();
        } else {
            // Use attached questions when not random
            $questions = $quiz->Questions; // attached questions
            if ((int)($quiz->is_shuffle ?? 0) === 1) {
                $questions = $questions instanceof \Illuminate\Support\Collection ? $questions->shuffle()->values() : collect($questions)->shuffle()->values();
            } else {
                if ($questions instanceof \Illuminate\Support\Collection) {
                    $questions = $questions->sortBy(function($q){ return optional($q->pivot)->order ?? 0; })->values();
                }
            }
        }

        foreach ($questions as $question) {
            WorkoutQuizLog::create([
                'workout_id' => $workout->id,
                'quiz_id' => $quiz->id,
                'question_id' => $question->id,
            ]);
        }

        return null;
    }

    /**
     * Recompute workout score and mark completion.
     */
    public static function recomputeScore(Workout $workout): int
    {
        $logs = $workout->WorkOutQuiz;
        if (!$logs || $logs->count() === 0) {
            $workout->update([
                'score' => 0,
                'is_completed' => false,
                'is_mentor' => false,
                'date_get_score' => now(),
            ]);
            return 0;
        }

        $sumOfScore = 0;
        foreach ($logs as $log) {
            $sumOfScore += (int) ($log->score ?? 0);
        }
        $score = (int) ($sumOfScore / max(1, count($logs)));

        $workout->update([
            'score' => $score,
            'is_completed' => true, // consider finished when logs exist
            'is_mentor' => false,
            'date_get_score' => now(),
        ]);

        return $score;
    }
}