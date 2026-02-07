<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-body py-2">
                <div class="small text-muted mb-2">Dynamic Difficulty Adjustment</div>
                <div id="dda-card" class="d-flex align-items-center justify-content-between" style="gap:16px;">
                    <div>
                        <div id="dda-status" class="text-muted">Klik tombol untuk menganalisa hasil pengerjaan dan mendapatkan rekomendasi soal berikutnya.</div>
                        <div id="dda-result" class="h6 m-0 mt-2"></div>
                    </div>
                    <div>
                        <button class="btn btn-info" id="dda-analyze">Analyze & Recommend</button>
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

    const btn = document.getElementById('dda-analyze');
    const status = document.getElementById('dda-status');
    const result = document.getElementById('dda-result');

    btn.addEventListener('click', async function (e) {
        e.preventDefault();
        status.textContent = 'Analyzing...';
        result.textContent = '';
        try {
            const resp = await fetch('http://127.0.0.1:8001/recommend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!resp.ok) throw new Error('Network response was not ok');
            const data = await resp.json();
            status.textContent = 'Recommendation:';
            result.textContent = `Difficulty: ${data.next_difficulty}  Topic: ${data.next_topic}`;

            if (confirm(`Apply recommendation (Difficulty: ${data.next_difficulty}) and restart quiz?`)) {
                const form = document.createElement('form');
                form.method = 'post';
                form.className = 'd-inline';
                form.action = "{{ route('quizRestart', $workout->id) }}";

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                const inputDifficulty = document.createElement('input');
                inputDifficulty.type = 'hidden';
                inputDifficulty.name = 'dda_difficulty';
                inputDifficulty.value = data.next_difficulty;

                form.appendChild(csrf);
                form.appendChild(inputDifficulty);

                const btn2 = document.createElement('button');
                btn2.className = 'btn btn-warning';
                btn2.id = 'restartQuizDDA';
                btn2.innerHTML = '<i class="fa fa-redo"></i> Restart with DDA';
                form.appendChild(btn2);

                document.querySelector('.card-footer.text-center')?.appendChild(form);
            }
        } catch (err) {
            status.textContent = 'Failed to get recommendation (service offline).';
            console.error(err);
        }
    });
});
</script>
