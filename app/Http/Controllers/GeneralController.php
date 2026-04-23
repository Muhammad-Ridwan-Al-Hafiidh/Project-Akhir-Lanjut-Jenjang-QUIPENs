<?php

namespace App\Http\Controllers;

use App\Services\Units\Coins\UserCoins;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function dashboard(UserCoins $userCoins)
    {
        $user = Auth::user();
        $user->badge = $userCoins->getUserBadge($user);
        $user->topicLevels = $this->getTopicLevelsMastery();

        return view('contents.dashboard.index', compact('user'));
    }

    private function getTopicLevelsMastery()
    {
        $user = Auth::user();
        $topicLevels = [];

        $participants = $user->Participants()->get();

        foreach ($participants as $participant) {
            $workouts = $participant->Workout()
                ->where('is_completed', 1)
                ->with(['RestartLogs', 'WorkOutQuiz.Question'])
                ->get();

            foreach ($workouts as $workout) {
                $logs = $workout->RestartLogs;
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

                        $topicLevels[$topic] = max($topicLevels[$topic] ?? 0, $level);
                    }
                }
            }
        }

        arsort($topicLevels);
        ksort($topicLevels);

        return $topicLevels;
    }
}
