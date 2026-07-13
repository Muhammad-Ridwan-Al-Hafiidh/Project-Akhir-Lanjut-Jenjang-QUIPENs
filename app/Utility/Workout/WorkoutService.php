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

        $topicsArray = $quiz->getTopicsArray();
        $hasTopics = !empty($topicsArray);
        $n = (int) ($quiz->random_question ?? 0);
        $difficultyCounts = [
            'easy' => (int) ($quiz->easy_questions_count ?? 0),
            'medium' => (int) ($quiz->medium_questions_count ?? 0),
            'hard' => (int) ($quiz->hard_questions_count ?? 0),
        ];
        $hasDifficultyCounts = array_sum($difficultyCounts) > 0;
        $questions = collect();

        if ($hasDifficultyCounts) {
            $baseQuery = Question::query();
            if ($hasTopics) {
                $baseQuery->whereIn('topic', $topicsArray);
            }

            $selectedIds = [];
            foreach (['easy', 'medium', 'hard'] as $level) {
                $count = $difficultyCounts[$level];
                if ($count <= 0) {
                    continue;
                }

                $priorityQuestions = (clone $baseQuery)
                    ->where('difficulty', $level)
                    ->inRandomOrder()
                    ->get();

                $picked = $priorityQuestions->take($count);
                $questions = $questions->merge($picked);
                $selectedIds = array_merge($selectedIds, $picked->pluck('id')->all());

                if ($picked->count() < $count) {
                    $remaining = $count - $picked->count();
                    $supplement = (clone $baseQuery)
                        ->whereNotIn('id', $selectedIds)
                        ->inRandomOrder()
                        ->limit($remaining)
                        ->get();
                    $questions = $questions->merge($supplement);
                    $selectedIds = array_merge($selectedIds, $supplement->pluck('id')->all());
                }
            }

            if ($questions->count() === 0) {
                $questions = (clone $baseQuery)->inRandomOrder()->limit(array_sum($difficultyCounts))->get();
            }

            if ((int) ($quiz->is_shuffle ?? 0) === 1) {
                $questions = $questions->unique('id')->shuffle()->values();
            } else {
                $questions = $questions->unique('id')->values();
            }
        } elseif ($n > 0) {
            $baseQuery = Question::query();
            if ($hasTopics) {
                $baseQuery->whereIn('topic', $topicsArray);
            }

            if ($difficulty) {
                $priorityQuestions = (clone $baseQuery)
                    ->where('difficulty', $difficulty)
                    ->inRandomOrder()
                    ->get();

                $questions = $questions->merge($priorityQuestions);

                if ($questions->count() < $n) {
                    $supplementQuestions = (clone $baseQuery)
                        ->where('difficulty', '!=', $difficulty)
                        ->inRandomOrder()
                        ->limit($n - $questions->count())
                        ->get();

                    $questions = $questions->merge($supplementQuestions);
                }
            } else {
                $questions = $baseQuery->inRandomOrder()->limit($n)->get();
            }

            $questions = $questions->take($n);
        } else {
            $questions = $quiz->Questions;

            if ($hasTopics) {
                $questions = $questions->filter(function ($q) use ($topicsArray) {
                    return in_array($q->topic, $topicsArray);
                })->values();
            }

            if ($questions->count() === 0 && $hasTopics) {
                $questions = Question::whereIn('topic', $topicsArray)->get();
            }

            if ($questions->count() === 0) {
                $questions = Question::all();
            }

            if ($difficulty) {
                $filtered = collect($questions)->filter(function ($q) use ($difficulty) {
                    return isset($q->difficulty) ? ($q->difficulty == $difficulty) : false;
                })->values();

                if ($filtered->count() === 0) {
                    $questions = collect($questions);
                } else {
                    $questions = $filtered;
                }
            } else {
                $questions = collect($questions);
            }

            if ((int) ($quiz->is_shuffle ?? 0) === 1) {
                $questions = $questions->shuffle()->values();
            } else {
                $questions = $questions->sortBy(function ($q) {
                    return optional($q->pivot)->order ?? 0;
                })->values();
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
            'is_completed' => true,
            'is_mentor' => false,
            'date_get_score' => now(),
        ]);

        return $score;
    }
}
