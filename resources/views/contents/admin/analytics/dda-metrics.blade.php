@extends('layouts.admin')
@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-primary"></i>Evaluasi Metrik DDA
            </h1>
            <p class="text-muted small mb-0">Analisis performa soal dan hasil ujian berdasarkan topik yang dipilih</p>
        </div>
        <div>
            <span class="badge bg-info text-white p-2" id="selected-count">0 topik dipilih</span>
        </div>
    </div>

    <!-- Filter Topik -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-1"></i> Pilih Topik
            </h6>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-1" id="select-all-btn">Pilih Semua</button>
                <button class="btn btn-sm btn-outline-secondary me-2" id="clear-all-btn">Hapus Pilihan</button>
                <button id="analyze-btn" class="btn btn-primary btn-sm">
                    <i class="fas fa-play me-1"></i> Analisis
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="topic-list">
                @forelse ($topics as $topic)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                        <div class="form-check">
                            <input class="form-check-input topic-checkbox" type="checkbox" value="{{ $topic }}" id="topic_{{ $loop->index }}">
                            <label class="form-check-label text-truncate" for="topic_{{ $loop->index }}" title="{{ $topic }}">
                                {{ $topic }}
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">Tidak ada topik tersedia.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-2 text-end">
                <small class="text-muted" id="selected-hint">Belum ada topik dipilih</small>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading-spinner" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Sedang menganalisis data, mohon tunggu...</p>
    </div>

    <!-- Error -->
    <div id="error-msg" class="alert alert-danger" style="display: none;"></div>

    <!-- Results -->
    <div id="results" style="display: none;">

        <!-- Ringkasan per Topik -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-1"></i> Ringkasan per Topik
                </h6>
                <span class="badge bg-secondary" id="summary-count">0 topik</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle" id="topicSummaryTable">
                        <thead class="table-light">
                            <tr>
                                <th>Topik</th>
                                <th class="text-center">Jumlah Soal</th>
                                <th class="text-center">Total Dijawab</th>
                                <th class="text-center">Benar</th>
                                <th class="text-center">Success Rate</th>
                            </tr>
                        </thead>
                        <tbody id="topicSummaryBody"></tbody>
                        <tfoot id="topicSummaryFooter" style="display: none;">
                            <tr class="table-secondary fw-bold">
                                <td>Total Keseluruhan</td>
                                <td class="text-center" id="total-questions">0</td>
                                <td class="text-center" id="total-answers">0</td>
                                <td class="text-center" id="total-correct">0</td>
                                <td class="text-center" id="total-success-rate">0%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="no-topic-data" class="text-center text-muted py-3" style="display: none;">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada data ringkasan untuk topik yang dipilih.
                </div>
            </div>
        </div>

        <!-- Data Soal -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-question-circle me-1"></i> Data Soal yang Pernah Dijawab
                </h6>
                <span class="badge bg-secondary" id="question-count">0 soal</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle" id="questionStatsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Soal</th>
                                <th>Topik</th>
                                <th class="text-center">Difficulty</th>
                                <th class="text-center">Total Dijawab</th>
                                <th class="text-center">Benar</th>
                                <th class="text-center">Success Rate</th>
                            </tr>
                        </thead>
                        <tbody id="questionStatsBody"></tbody>
                    </table>
                </div>
                <div id="no-question-data" class="text-center text-muted py-3" style="display: none;">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada data soal untuk topik yang dipilih.
                </div>
            </div>
        </div>

        <!-- Hasil Ujian per Restart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-1"></i> Hasil Ujian Setiap Restart
                </h6>
                <span class="badge bg-secondary" id="restart-count">0 data</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle" id="restartResultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th class="text-center">Skor</th>
                                <th class="text-center">Mode</th>
                                <th class="text-center">Difficulty</th>
                                <th>Topic Levels</th>
                                <th class="text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="restartResultsBody"></tbody>
                    </table>
                </div>
                <div id="no-restart-data" class="text-center text-muted py-3" style="display: none;">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada data ujian untuk topik yang dipilih.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .progress {
        height: 6px;
        border-radius: 4px;
    }
    .topic-checkbox:checked + label {
        font-weight: 600;
        color: #0d6efd;
    }
    .form-check {
        padding-left: 1.75rem;
    }
    .form-check-input {
        cursor: pointer;
    }
    .form-check-label {
        cursor: pointer;
        font-size: 0.9rem;
    }
    #selected-hint {
        font-style: italic;
    }
    @media (max-width: 576px) {
        .card-header .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .table-sm {
            font-size: 0.8rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DOM refs ---
        const checkboxes = document.querySelectorAll('.topic-checkbox');
        const selectAllBtn = document.getElementById('select-all-btn');
        const clearAllBtn = document.getElementById('clear-all-btn');
        const analyzeBtn = document.getElementById('analyze-btn');
        const selectedCount = document.getElementById('selected-count');
        const selectedHint = document.getElementById('selected-hint');
        const loadingSpinner = document.getElementById('loading-spinner');
        const errorDiv = document.getElementById('error-msg');
        const resultsDiv = document.getElementById('results');

        // --- update topic selection info ---
        function updateTopicInfo() {
            const checked = document.querySelectorAll('.topic-checkbox:checked');
            const count = checked.length;
            const total = checkboxes.length;
            selectedCount.textContent = count + ' topik dipilih';
            if (count === 0) {
                selectedHint.textContent = 'Belum ada topik dipilih';
                selectedHint.className = 'text-muted';
            } else {
                const names = Array.from(checked).map(cb => cb.nextElementSibling.textContent.trim());
                selectedHint.textContent = 'Terpilih: ' + names.join(', ');
                selectedHint.className = 'text-success';
            }
        }

        // --- event listeners for select/deselect ---
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = true);
            updateTopicInfo();
        });
        clearAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateTopicInfo();
        });
        checkboxes.forEach(cb => cb.addEventListener('change', updateTopicInfo));
        updateTopicInfo();

        // --- analyze ---
        analyzeBtn.addEventListener('click', analyze);

        async function analyze() {
            const topicIds = Array.from(document.querySelectorAll('.topic-checkbox:checked')).map(cb => cb.value);
            if (topicIds.length === 0) {
                alert('Pilih minimal satu topik untuk dianalisis.');
                return;
            }

            // Show loading, hide results & error
            loadingSpinner.style.display = 'block';
            resultsDiv.style.display = 'none';
            errorDiv.style.display = 'none';
            analyzeBtn.disabled = true;

            try {
                const response = await fetch(`{{ route('api.analytics.dda-metrics') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': `{{ csrf_token() }}`
                    },
                    body: JSON.stringify({ topic_ids: topicIds })
                });

                const result = await response.json();
                if (result.success) {
                    displayResults(result);
                    resultsDiv.style.display = 'block';
                } else {
                    errorDiv.textContent = result.message || 'Gagal memuat data.';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error(error);
                errorDiv.textContent = 'Terjadi kesalahan jaringan: ' + error.message;
                errorDiv.style.display = 'block';
            } finally {
                loadingSpinner.style.display = 'none';
                analyzeBtn.disabled = false;
            }
        }

        // --- display results ---
        function displayResults(data) {
            // 1. Topic Summary
            const summaryBody = document.getElementById('topicSummaryBody');
            const footer = document.getElementById('topicSummaryFooter');
            const totalQ = document.getElementById('total-questions');
            const totalA = document.getElementById('total-answers');
            const totalC = document.getElementById('total-correct');
            const totalSR = document.getElementById('total-success-rate');
            const summaryCount = document.getElementById('summary-count');
            const noTopic = document.getElementById('no-topic-data');

            let htmlSummary = '';
            let sumQuestions = 0, sumAnswers = 0, sumCorrect = 0;

            (data.topic_summary || []).forEach(t => {
                const rate = t.success_rate || 0;
                let badgeClass = 'bg-success';
                if (rate < 40) badgeClass = 'bg-danger';
                else if (rate < 70) badgeClass = 'bg-warning';

                htmlSummary += `
                    <tr>
                        <td><strong>${t.topic}</strong></td>
                        <td class="text-center">${t.total_questions}</td>
                        <td class="text-center">${t.total_answers}</td>
                        <td class="text-center">${t.correct_count}</td>
                        <td class="text-center">
                            <span class="badge ${badgeClass} text-white">${rate}%</span>
                            <div class="progress mt-1">
                                <div class="progress-bar ${badgeClass}" role="progressbar" style="width: ${Math.min(rate, 100)}%;" aria-valuenow="${rate}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                `;
                sumQuestions += t.total_questions;
                sumAnswers += t.total_answers;
                sumCorrect += t.correct_count;
            });

            summaryBody.innerHTML = htmlSummary;

            if (data.topic_summary && data.topic_summary.length > 0) {
                footer.style.display = 'table-row';
                totalQ.textContent = sumQuestions;
                totalA.textContent = sumAnswers;
                totalC.textContent = sumCorrect;
                const overallRate = sumAnswers > 0 ? Math.round((sumCorrect / sumAnswers) * 100) : 0;
                totalSR.textContent = overallRate + '%';
                summaryCount.textContent = data.topic_summary.length + ' topik';
                noTopic.style.display = 'none';
            } else {
                footer.style.display = 'none';
                summaryCount.textContent = '0 topik';
                noTopic.style.display = 'block';
            }

            // 2. Question Stats
            const questionBody = document.getElementById('questionStatsBody');
            const questionCount = document.getElementById('question-count');
            const noQuestion = document.getElementById('no-question-data');

            if (data.question_stats && data.question_stats.length > 0) {
                noQuestion.style.display = 'none';
                let htmlQ = '';
                data.question_stats.forEach(q => {
                    const rate = q.success_rate || 0;
                    let badgeClass = 'bg-success';
                    if (rate < 40) badgeClass = 'bg-danger';
                    else if (rate < 70) badgeClass = 'bg-warning';

                    let diffClass = 'bg-secondary';
                    if (q.difficulty === 'easy') diffClass = 'bg-success';
                    else if (q.difficulty === 'medium') diffClass = 'bg-warning';
                    else if (q.difficulty === 'hard') diffClass = 'bg-danger';

                    htmlQ += `
                        <tr>
                            <td>${q.question_body}</td>
                            <td>${q.topic}</td>
                            <td class="text-center"><span class="badge ${diffClass} text-white">${q.difficulty}</span></td>
                            <td class="text-center">${q.total_answers}</td>
                            <td class="text-center">${q.correct_count}</td>
                            <td class="text-center">
                                <span class="badge ${badgeClass} text-white">${rate}%</span>
                                <div class="progress mt-1">
                                    <div class="progress-bar ${badgeClass}" role="progressbar" style="width: ${Math.min(rate, 100)}%;" aria-valuenow="${rate}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                questionBody.innerHTML = htmlQ;
                questionCount.textContent = data.question_stats.length + ' soal';
            } else {
                noQuestion.style.display = 'block';
                questionBody.innerHTML = '';
                questionCount.textContent = '0 soal';
            }

            // 3. Restart Results
            const restartBody = document.getElementById('restartResultsBody');
            const restartCount = document.getElementById('restart-count');
            const noRestart = document.getElementById('no-restart-data');

            if (data.restart_results && data.restart_results.length > 0) {
                noRestart.style.display = 'none';
                let htmlR = '';
                data.restart_results.forEach(r => {
                    const modeBadge = r.used_dda ? 'bg-info' : 'bg-secondary';
                    const modeText = r.used_dda ? 'DDA' : 'Non-DDA';
                    const diffBadge = r.difficulty === 'easy' ? 'bg-success' : (r.difficulty === 'medium' ? 'bg-warning' : 'bg-danger');
                    // topic levels
                    let levels = '-';
                    let topicData = r.topic_levels;
                    if (typeof topicData === 'string') {
                        try { topicData = JSON.parse(topicData); } catch(e) { topicData = null; }
                    }
                    if (topicData && typeof topicData === 'object') {
                        const entries = Object.entries(topicData);
                        if (entries.length) {
                            levels = entries.map(([t, l]) => {
                                const lv = parseInt(l);
                                let label = '', cls = '';
                                if (lv >= 3) { label = 'Kuat'; cls = 'bg-success'; }
                                else if (lv >= 1) { label = 'Sedang'; cls = 'bg-warning text-dark'; }
                                else { label = 'Rendah'; cls = 'bg-danger'; }
                                return `<span class="badge ${cls} text-white me-1">${t}: ${label} (level ${lv})</span>`;
                            }).join(' ');
                        }
                    }
                    htmlR += `
                        <tr>
                            <td><strong>${r.student_name}</strong></td>
                            <td class="text-center"><span class="fw-bold">${r.score}</span></td>
                            <td class="text-center"><span class="badge ${modeBadge} text-white">${modeText}</span></td>
                            <td class="text-center"><span class="badge ${diffBadge} text-white">${r.difficulty}</span></td>
                            <td>${levels}</td>
                            <td class="text-center">${r.created_at}</td>
                        </tr>
                    `;
                });
                restartBody.innerHTML = htmlR;
                restartCount.textContent = data.restart_results.length + ' data';
            } else {
                noRestart.style.display = 'block';
                restartBody.innerHTML = '';
                restartCount.textContent = '0 data';
            }
        }
    });
</script>
@endsection
