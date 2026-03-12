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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logs = {!! json_encode($workout->WorkOutQuiz->map(function($l) use ($activity) { return [
        'question_id' => $l->question_id,
        'difficulty' => 'medium',
        'is_correct' => ($l->score ?? 0) > 0,
        'topic' => $activity->title ?? 'general',
        'answer_time_seconds' => null
    ]; })) !!};

    const payload = {
        user_id: '{{ optional($participant)->id ?? optional($workout->User)->id ?? '' }}',
        session_logs: logs
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
            
            recommendationDiv.innerHTML = `
                <div class="row g-2">
                    <div class="col-auto">
                        <span class="badge bg-primary">Next Difficulty: <strong>${nextDifficulty}</strong></span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success">Topic: <strong>${nextTopic}</strong></span>
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