<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-sync-alt me-2"></i> Restart Options</h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-3">
                    {{-- DDA Restart Card --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-robot text-info fa-2x mb-2"></i>
                                <h6 class="card-title"><i class="fas fa-robot me-1"></i> Restart with DDA</h6>
                                <p class="card-text text-muted small">
                                    Restart quiz with Dynamic Difficulty Adjustment. Questions will adapt to your level.
                                </p>
                                <button type="button" class="btn btn-info btn-sm" id="btn-dda-analyze">
                                    <i class="fas fa-brain me-1"></i> Analyze & Restart
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Non-DDA Restart Card --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-secondary h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-ban text-secondary fa-2x mb-2"></i>
                                <h6 class="card-title"><i class="fas fa-ban me-1"></i> Restart without DDA</h6>
                                <p class="card-text text-muted small">
                                    Restart quiz with standard fixed difficulty. No AI adaptation.
                                </p>
                                <form method="POST" action="{{ route('quizRestart', $workout->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="used_dda" value="0">
                                    <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Restart quiz without DDA? Previous answers will be cleared.')">
                                        <i class="fas fa-redo me-1"></i> Restart Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DDA Analysis Result --}}
                <div id="dda-result-section" class="d-none mt-3">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle me-2"></i> DDA Recommendation:</strong>
                        <div id="dda-recommendation" class="mt-2"></div>
                        <form method="POST" action="{{ route('quizRestart', $workout->id) }}" class="d-inline mt-2">
                            @csrf
                            <input type="hidden" name="used_dda" value="1">
                            <input type="hidden" id="dda-difficulty-input" name="dda_difficulty" value="">
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Apply DDA recommendation and restart quiz?')">
                                <i class="fas fa-check-circle me-1"></i> Apply & Restart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $workoutLogsForDda = $workout->WorkOutQuiz()->with('Question')->get();

    $ddaLogs = $workoutLogsForDda->map(function ($l) {
        $question = $l->Question;

        return [
            'question_id' => (int) $l->question_id,
            'difficulty' => $question->difficulty ?? 'medium',
            'is_correct' => ((int) ($l->score ?? 0)) > 0,
            'topic' => $question->topic ?? 'general',
            'answer_time_seconds' => null,
        ];
    })->values();

    $topicStatsForDda = [];
    foreach ($workoutLogsForDda as $quizLog) {
        if ($quizLog->Question && $quizLog->Question->topic) {
            $topic = $quizLog->Question->topic;
            if (!isset($topicStatsForDda[$topic])) {
                $topicStatsForDda[$topic] = ['total_score' => 0, 'total_count' => 0];
            }
            $topicStatsForDda[$topic]['total_score'] += (int) ($quizLog->score ?? 0);
            $topicStatsForDda[$topic]['total_count'] += 1;
        }
    }

    $topicLevelsForDda = [];
    foreach ($topicStatsForDda as $topic => $stats) {
        if ($stats['total_count'] > 0) {
            $accuracy = ($stats['total_score'] / ($stats['total_count'] * 100)) * 100;
            $topicLevelsForDda[$topic] = match (true) {
                $accuracy >= 80 => 4,
                $accuracy >= 60 => 3,
                $accuracy >= 40 => 2,
                $accuracy > 0 => 1,
                default => 0,
            };
        }
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logs = @json($ddaLogs);
    const topicLevels = @json($topicLevelsForDda);

    const payload = {
        user_id: '{{ optional($participant)->id ?? optional($workout->User)->id ?? '' }}',
        session_logs: logs,
        topic_levels: topicLevels
    };

    const btnAnalyze = document.getElementById('btn-dda-analyze');
    const resultSection = document.getElementById('dda-result-section');
    const recommendationDiv = document.getElementById('dda-recommendation');
    const difficultyInput = document.getElementById('dda-difficulty-input');

    btnAnalyze.addEventListener('click', async function (e) {
        e.preventDefault();
        btnAnalyze.disabled = true;
        btnAnalyze.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Analyzing...';

        try {
            const resp = await fetch('http://127.0.0.1:8001/recommend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!resp.ok) throw new Error('Network response was not ok');

            const data = await resp.json();
            const nextDifficulty = data.next_difficulty || 'medium';
            const nextTopic = data.next_topic || 'general';
            const recommendedTopicLevel = data.recommended_topic_level;

            const topicEntries = Object.entries(topicLevels || {});
            const sortedTopics = topicEntries.sort((a, b) => {
                if (a[1] === b[1]) return String(a[0]).localeCompare(String(b[0]));
                return a[1] - b[1];
            });
            const weakestIndex = sortedTopics.findIndex(([topic]) => topic === nextTopic);
            const weakestRankText = weakestIndex >= 0
                ? `${weakestIndex + 1} dari ${sortedTopics.length}`
                : 'N/A';

            const topicLevel = recommendedTopicLevel ?? (topicLevels[nextTopic] ?? 0);

            let focusReason = 'Topik ini dipilih sebagai prioritas adaptif berdasarkan performa terakhir.';
            if (topicLevel <= 1) {
                focusReason = 'Level topik masih rendah, jadi sistem memprioritaskan remedial pada topik ini.';
            } else if (topicLevel === 2) {
                focusReason = 'Level topik menengah, jadi sistem mendorong penguatan konsep sebelum naik level.';
            } else if (topicLevel >= 3) {
                focusReason = 'Topik ini sudah cukup kuat, direkomendasikan untuk stabilisasi dan konsistensi.';
            }

            let difficultyReason = 'Difficulty menyesuaikan kombinasi akurasi sesi dan level topik saat ini.';
            if (nextDifficulty === 'easy') {
                difficultyReason = 'Dipilih easy untuk membangun confidence dan akurasi pada topik prioritas.';
            } else if (nextDifficulty === 'medium') {
                difficultyReason = 'Dipilih medium untuk menantang tanpa membuat beban terlalu tinggi.';
            } else if (nextDifficulty === 'hard') {
                difficultyReason = 'Dipilih hard karena performa menunjukkan kesiapan untuk tantangan lebih tinggi.';
            }

            recommendationDiv.innerHTML = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary text-white px-2">Next Difficulty: <strong>${nextDifficulty}</strong></span>
                            <span class="badge bg-success text-white px-2">Topic: <strong>${nextTopic}</strong></span>
                            <span class="badge bg-info text-white px-2">Topic Level: <strong>L${topicLevel}</strong></span>
                        </div>

                        <div class="small text-muted mb-2"><strong>Alasan Rekomendasi</strong></div>
                        <div class="border rounded p-2 bg-light mb-2">
                            <div class="small"><i class="fas fa-bullseye me-1 text-success"></i>${focusReason}</div>
                            <div class="small mt-1"><i class="fas fa-sliders-h me-1 text-primary"></i>${difficultyReason}</div>
                        </div>

                        <div class="small text-muted">
                            Prioritas topik saat ini: <strong>${weakestRankText}</strong>
                        </div>
                    </div>
                </div>
            `;

            difficultyInput.value = nextDifficulty;
            resultSection.classList.remove('d-none');
            btnAnalyze.innerHTML = '<i class="fas fa-check-circle me-1"></i> Analysis Complete';
            btnAnalyze.disabled = false;

        } catch (err) {
            recommendationDiv.innerHTML = `<div class="alert alert-warning"><small>Unable to get recommendation (service offline). Defaulting to Medium difficulty.</small></div>`;
            difficultyInput.value = 'medium';
            resultSection.classList.remove('d-none');
            btnAnalyze.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Try Again';
            btnAnalyze.disabled = false;
            console.error(err);
        }
    });
});
</script>



