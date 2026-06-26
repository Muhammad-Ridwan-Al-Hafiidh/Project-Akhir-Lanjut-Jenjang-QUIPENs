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

    public static function setWorkOutQuizSyncForThisExcersice(Workout $workout, Quiz $quiz, ?string $difficulty = null): ?array
    {
        if ($workout->WorkOutQuiz->count() > 0) {
            return [];
        }

        // Get topics for this quiz
        $topicsArray = $quiz->getTopicsArray();
        $hasTopics = !empty($topicsArray);
        
        // Check if random_question is set
        $n = (int) ($quiz->random_question ?? 0);
        $questions = collect();

        if ($n > 0) {
            // Use N random questions from bank, filtered by topics
            // PRIORITY: Take from recommended difficulty first, then supplement from other difficulties
            
            $baseQuery = Question::query();
            if ($hasTopics) {
                $baseQuery->whereIn('topic', $topicsArray);
            }

            if ($difficulty) {
                // Step 1: Get questions from recommended difficulty
                $priorityQuestions = (clone $baseQuery)
                    ->where('difficulty', $difficulty)
                    ->inRandomOrder()
                    ->get();
                
                $questions = $questions->merge($priorityQuestions);
                
                // Step 2: If not enough, supplement from other difficulties
                if ($questions->count() < $n) {
                    $supplementQuestions = (clone $baseQuery)
                        ->where('difficulty', '!=', $difficulty)
                        ->inRandomOrder()
                        ->limit($n - $questions->count())
                        ->get();
                    
                    $questions = $questions->merge($supplementQuestions);
                }
            } else {
                // No difficulty specified, just take N random questions
                $questions = $baseQuery->inRandomOrder()->limit($n)->get();
            }

            // Ensure we have exactly N questions (or less if bank is too small)
            $questions = $questions->take($n);
        } else {
            // Use attached questions, filtered by topics
            $questions = $quiz->Questions;
            
            if ($hasTopics) {
                $questions = $questions->filter(function($q) use ($topicsArray) {
                    return in_array($q->topic, $topicsArray);
                })->values();
            }

            // Fallback to question bank if no attached questions remain
            if ($questions->count() === 0 && $hasTopics) {
                $questions = Question::whereIn('topic', $topicsArray)->get();
            }

            // Final fallback: all questions
            if ($questions->count() === 0) {
                $questions = Question::all();
            }

            // Apply difficulty filter with fallback logic
            if ($difficulty) {
                $filtered = collect($questions)->filter(function ($q) use ($difficulty) {
                    return isset($q->difficulty) ? ($q->difficulty == $difficulty) : false;
                })->values();

                // If filtered result is empty or too small, use all questions
                if ($filtered->count() === 0) {
                    $questions = collect($questions);
                } else {
                    $questions = $filtered;
                }
            } else {
                $questions = collect($questions);
            }

            // Apply shuffle/sort
            if ((int)($quiz->is_shuffle ?? 0) === 1) {
                $questions = $questions->shuffle()->values();
            } else {
                $questions = $questions->sortBy(function($q){ return optional($q->pivot)->order ?? 0; })->values();
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

