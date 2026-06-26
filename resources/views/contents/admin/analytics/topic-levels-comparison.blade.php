@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-gradient-primary text-white">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar me-2"></i> Topic Level Comparison: DDA vs Non-DDA
                    </h5>
                    <x-BackButton />
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Compare student performance across topics in DDA (Dynamic Difficulty Adjustment) vs Non-DDA modes.
                        Data is aggregated from restart logs and quiz answers per student per topic.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-palette me-2"></i> Level Legend</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-auto"><span class="badge bg-success p-2"><i class="fas fa-star me-1"></i> Level 4 (80-100%)</span></div>
                        <div class="col-auto"><span class="badge bg-info p-2"><i class="fas fa-check-circle me-1"></i> Level 3 (60-79%)</span></div>
                        <div class="col-auto"><span class="badge bg-warning text-dark p-2"><i class="fas fa-exclamation-circle me-1"></i> Level 2 (40-59%)</span></div>
                        <div class="col-auto"><span class="badge bg-secondary p-2"><i class="fas fa-circle me-1"></i> Level 1 (1-39%)</span></div>
                        <div class="col-auto"><span class="badge bg-light text-dark border p-2"><i class="fas fa-times me-1"></i> Level 0 (0%)</span></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 text-muted"><i class="fas fa-filter me-2"></i> Analisis Mahasiswa</h6>
                    <small class="text-muted">Pilih term dan mahasiswa untuk membangun rekap SPK</small>
                </div>
                <div class="card-body">
                    @if($terms->count() > 0)
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold" for="term_id">Term</label>
                                <select class="form-select" id="term_id">
                                    <option value="">-- Pilih term --</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->title ?: "Term #".$term->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-bold">Mahasiswa</label>
                                <div id="student-list" class="border rounded p-3 bg-light">
                                    <div class="text-muted small">Pilih term untuk memuat daftar mahasiswa.</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary" id="btn-refresh-analysis" onclick="loadAnalysis()">
                                <i class="fas fa-play me-1"></i> Jalankan Analisis
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">Tidak ada term yang tersedia untuk analisis.</div>
                    @endif
                </div>
            </div>

            <div id="analysis-summary" class="mb-4"></div>

            <div class="card shadow-sm">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-chart-line me-2"></i> Analysis Modes</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs border-bottom-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-nondda" data-bs-toggle="tab" data-bs-target="#content-nondda" type="button" role="tab">
                                <i class="fas fa-ban me-1"></i> Non-DDA Mode
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-dda" data-bs-toggle="tab" data-bs-target="#content-dda" type="button" role="tab">
                                <i class="fas fa-robot me-1"></i> DDA Mode
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-comparison" data-bs-toggle="tab" data-bs-target="#content-comparison" type="button" role="tab">
                                <i class="fas fa-balance-scale me-1"></i> Comparison
                            </button>
                        </li>
                                                <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-dda-metrics" data-bs-toggle="tab" data-bs-target="#content-dda-metrics" type="button" role="tab">
                                <i class="fas fa-robot me-1"></i> DDA Metrics
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-spk" data-bs-toggle="tab" data-bs-target="#content-spk" type="button" role="tab">
                                <i class="fas fa-user-graduate me-1"></i> Rekap SPK
                            </button>
                        </li>
                        <li class="nav-item ms-auto" role="presentation">
                            <span class="nav-link disabled text-muted small">
                                <i class="fas fa-spinner fa-spin me-1 d-none" id="loading-spinner"></i>
                                <span id="loading-text"></span>
                            </span>
                        </li>
                    </ul>

                    <div class="tab-content p-4">
                        <div class="tab-pane fade show active" id="content-nondda" role="tabpanel">
                            <div id="nondda-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                                    <span class="text-muted">Loading Non-DDA analysis...</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="content-dda" role="tabpanel">
                            <div id="dda-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                                    <span class="text-muted">Loading DDA analysis...</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="content-comparison" role="tabpanel">
                            <div id="comparison-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted">Loading comparison analysis...</span>
                                </div>
                            </div>
                        </div>

                                                <div class="tab-pane fade" id="content-dda-metrics" role="tabpanel">
                            <div id="dda-metrics-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                                    <span class="text-muted">Loading DDA metrics...</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="content-spk" role="tabpanel">
                            <div id="spk-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                    <span class="text-muted">Loading SPK recommendation...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const analysisCharts = {};
const studentsApiUrl = @json(route('api.topic-comparison.students'));
const comparisonDataUrl = @json(route('analytics.topic-levels-comparison.data'));
const ddaMetricsUrl = @json(route('api.topic-comparison.dda-metrics'));
const spkRecommendationsUrl = @json(route('api.topic-comparison.spk-recommendations'));

document.addEventListener('DOMContentLoaded', function() {
    const termSelect = document.getElementById('term_id');
    if (termSelect) {
        termSelect.addEventListener('change', async function() {
            await fetchStudents(this.value, false);
        });
    }

    document.getElementById('btn-refresh-analysis')?.addEventListener('click', loadAnalysis);
    window.loadAnalysis = loadAnalysis;

    refreshStudentListFromSelectedTerm();
});

window.addEventListener('pageshow', function() {
    refreshStudentListFromSelectedTerm();
});

function refreshStudentListFromSelectedTerm() {
    const termSelect = document.getElementById('term_id');
    if (!termSelect) {
        return;
    }

    const termId = termSelect.value;
    if (!termId) {
        return;
    }

    setTimeout(() => {
        fetchStudents(termId, false);
    }, 100);
}

async function fetchStudents(termId, autoLoad = false) {
    const container = document.getElementById('student-list');
    if (!container) return;

    if (!termId) {
        container.innerHTML = '<div class="text-muted small">Pilih term terlebih dahulu.</div>';
        return;
    }

    container.innerHTML = '<div class="text-muted small">Memuat data mahasiswa...</div>';

    try {
        const response = await fetch(`${studentsApiUrl}?term_id=${encodeURIComponent(termId)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Gagal memuat mahasiswa');
        }

        const students = data.students || [];
        if (students.length === 0) {
            container.innerHTML = '<div class="text-muted small">Tidak ada mahasiswa pada term ini.</div>';
            return;
        }

        container.innerHTML = students.map(student => {
            const checkboxId = `student_${student.id}`;
            return `
                <div class="form-check form-check-inline me-3 mb-2">
                    <input class="form-check-input student-checkbox" type="checkbox" id="${checkboxId}" value="${student.id}" checked>
                    <label class="form-check-label small" for="${checkboxId}">${student.name}</label>
                </div>
            `;
        }).join('');

        if (autoLoad) {
            await loadAnalysis();
        }
    } catch (error) {
        console.error(error);
        container.innerHTML = '<div class="text-danger small">Gagal memuat mahasiswa.</div>';
    }
}

async function loadAnalysis() {
    const termId = document.getElementById('term_id')?.value;
    const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);

    if (!termId) {
        alert('Pilih term terlebih dahulu');
        return;
    }

    if (!studentIds.length) {
        alert('Pilih setidaknya satu mahasiswa');
        return;
    }

    setAnalysisLoadingState(true);

    try {
        const response = await fetch(`${comparisonDataUrl}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                term_id: termId,
                student_ids: studentIds
            })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Gagal memuat analisis');
        }

        renderAnalysis(data);
    } catch (error) {
        console.error(error);
        showError(error.message);
    } finally {
        setAnalysisLoadingState(false);
    }
}

function renderAnalysis(data) {
    const summary = document.getElementById('analysis-summary');
    if (summary) {
        summary.innerHTML = `
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex flex-wrap gap-4">
                    <div><strong>${data.stats?.overall?.total_students ?? 0}</strong> mahasiswa dipilih</div>
                    <div><strong>${data.topics?.length ?? 0}</strong> topik dianalisis</div>
                    <div><strong>${formatTopicAverage(data.stats?.dda_avg)}</strong> rata-rata DDA</div>
                    <div><strong>${formatTopicAverage(data.stats?.nondda_avg)}</strong> rata-rata Non-DDA</div>
                </div>
            </div>
        `;
    }

    loadNonDDAAnalysis(data);
    loadDDAAnalysis(data);
    loadComparisonAnalysis(data);
    loadDDAMetrics(data);
    loadSPKRecommendations(data);
    
}

async function loadNonDDAAnalysis(data) {
    const container = document.getElementById('nondda-container');
    if (!container) return;

    if (!data.nondda_data || data.nondda_data.length === 0) {
        container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No Non-DDA data available yet.</div>';
        return;
    }

    container.innerHTML = `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> Non-DDA Statistics</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-secondary"><i class="fas fa-users me-2"></i>${data.stats.nondda_count}</div><small class="text-muted">Students with Non-DDA data</small></div></div>
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-secondary"><i class="fas fa-bookmark me-2"></i>${data.topics.length}</div><small class="text-muted">Topics</small></div></div>
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-secondary"><i class="fas fa-chart-line me-2"></i>${formatTopicAverage(data.stats.nondda_avg)}</div><small class="text-muted">Average Level</small></div></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-th me-2"></i> Heatmap (Non-DDA)</h6></div>
            <div class="card-body"><div class="table-responsive">${generateHeatmap(data.nondda_data, data.topics)}</div></div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> Average Levels by Topic</h6></div>
            <div class="card-body"><div class="row"><div class="col-12"><canvas id="nondda-avg-chart"></canvas></div></div></div>
        </div>
    `;

    setTimeout(() => {
        drawAverageChart('nondda-avg-chart', data.stats.nondda_avg, data.topics, 'Non-DDA Average Levels', '#858796');
    }, 100);
}

async function loadDDAAnalysis(data) {
    const container = document.getElementById('dda-container');
    if (!container) return;

    if (!data.dda_data || data.dda_data.length === 0) {
        container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No DDA data available yet.</div>';
        return;
    }

    container.innerHTML = `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> DDA Statistics</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-info"><i class="fas fa-users me-2"></i>${data.stats.dda_count}</div><small class="text-muted">Students with DDA data</small></div></div>
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-info"><i class="fas fa-bookmark me-2"></i>${data.topics.length}</div><small class="text-muted">Topics</small></div></div>
                    <div class="col-12 col-md-4"><div class="border rounded p-3 text-center bg-light"><div class="h5 mb-0 text-info"><i class="fas fa-chart-line me-2"></i>${formatTopicAverage(data.stats.dda_avg)}</div><small class="text-muted">Average Level</small></div></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-th me-2"></i> Heatmap (DDA)</h6></div>
            <div class="card-body"><div class="table-responsive">${generateHeatmap(data.dda_data, data.topics)}</div></div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> Average Levels by Topic</h6></div>
            <div class="card-body"><div class="row"><div class="col-12"><canvas id="dda-avg-chart"></canvas></div></div></div>
        </div>
    `;

    setTimeout(() => {
        drawAverageChart('dda-avg-chart', data.stats.dda_avg, data.topics, 'DDA Average Levels', '#36b9cc');
    }, 100);
}

async function loadComparisonAnalysis(data) {
    const container = document.getElementById('comparison-container');
    if (!container) return;

    const topics = data.topics || [];
    const ddaAvg = data.stats.dda_avg || {};
    const nonddaAvg = data.stats.nondda_avg || {};

    container.innerHTML = `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> Comparison Summary</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6"><div class="border rounded p-3 bg-light"><h6 class="text-muted mb-2"><i class="fas fa-ban me-2"></i> Non-DDA Mode</h6><div class="row g-2"><div class="col-6"><small class="text-muted">Students:</small><div class="fw-bold">${data.stats.nondda_count}</div></div><div class="col-6"><small class="text-muted">Avg Level:</small><div class="fw-bold text-secondary">${formatTopicAverage(nonddaAvg)}</div></div></div></div></div>
                    <div class="col-12 col-md-6"><div class="border rounded p-3 bg-light"><h6 class="text-muted mb-2"><i class="fas fa-robot me-2"></i> DDA Mode</h6><div class="row g-2"><div class="col-6"><small class="text-muted">Students:</small><div class="fw-bold">${data.stats.dda_count}</div></div><div class="col-6"><small class="text-muted">Avg Level:</small><div class="fw-bold text-info">${formatTopicAverage(ddaAvg)}</div></div></div></div></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> DDA vs Non-DDA by Topic</h6></div>
            <div class="card-body"><div class="row"><div class="col-12"><canvas id="comparison-chart"></canvas></div></div></div>
        </div>
    `;

    setTimeout(() => {
        drawComparisonChart('comparison-chart', ddaAvg, nonddaAvg, topics);
    }, 100);
}

function renderSPK(data) {
    const container = document.getElementById('spk-container');
    if (!container) return;

    const rows = (data.overall_data || []).map(student => {
        const levels = Object.values(student.levels || {}).map(value => Number(value) || 0);
        const average = averageFromValues(levels);
        let recommendation = 'Monitoring';
        if (average < 2) {
            recommendation = 'Intervensi';
        } else if (average >= 3) {
            recommendation = 'Rekomendasi Lanjut';
        }

        return `
            <tr>
                <td>${student.name || student.student_name || 'Unknown'}</td>
                <td class="text-center">${average.toFixed(2)}</td>
                <td>${recommendation}</td>
            </tr>
        `;
    }).join('');

    container.innerHTML = `
                        <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 text-muted"><i class="fas fa-user-graduate me-2"></i> Rekap SPK</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-export-spk">
                            <i class="fas fa-download me-1"></i> Export JSON
                        </button>
                    </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Ringkasan ini memberi rekomendasi awal berdasarkan rata-rata level topik yang dipilih.</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th class="text-center">Rata-Rata</th>
                                <th>Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows || '<tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>'}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function generateHeatmap(studentData, topics) {
    if (!studentData || studentData.length === 0) {
        return '<div class="alert alert-info mb-0">No data available</div>';
    }

    let html = '<table class="table table-striped table-hover mb-0 small">';
    html += '<thead class="table-light"><tr><th>Student</th>';

    topics.forEach(topic => {
        html += `<th class="text-center" title="${topic}">${topic.substring(0, 10)}</th>`;
    });

    html += '</tr></thead><tbody>';

    studentData.forEach(student => {
        html += `<tr><td class="fw-bold">${student.name || student.student_name || 'Unknown'}</td>`;
        topics.forEach(topic => {
            const level = student.levels?.[topic] || 0;
            html += `<td class="text-center"><span class="badge ${getLevelColor(level)} p-2">${level}</span></td>`;
        });
        html += '</tr>';
    });

    html += '</tbody></table>';
    return html;
}

function getLevelColor(level) {
    switch (parseInt(level, 10)) {
        case 4: return 'bg-success';
        case 3: return 'bg-info';
        case 2: return 'bg-warning text-dark';
        case 1: return 'bg-secondary';
        default: return 'bg-light text-dark border';
    }
}

function drawAverageChart(canvasId, avgData, topics, title, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const sortedTopics = [...topics].sort((a, b) => (avgData[b] || 0) - (avgData[a] || 0));
    const sortedData = sortedTopics.map(topic => Number(avgData[topic] || 0).toFixed(2));

    if (analysisCharts[canvasId]) {
        analysisCharts[canvasId].destroy();
    }

    analysisCharts[canvasId] = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: sortedTopics,
            datasets: [{
                label: title,
                data: sortedData,
                backgroundColor: color,
                borderColor: color,
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 5 } }
        }
    });
}

function drawComparisonChart(canvasId, ddaAvg, nonddaAvg, topics) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    if (analysisCharts[canvasId]) {
        analysisCharts[canvasId].destroy();
    }

    analysisCharts[canvasId] = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: topics,
            datasets: [
                {
                    label: 'DDA Mode',
                    data: topics.map(topic => Number(ddaAvg[topic] || 0).toFixed(2)),
                    backgroundColor: 'rgba(54, 185, 204, 0.8)',
                    borderColor: '#36b9cc',
                    borderWidth: 1
                },
                {
                    label: 'Non-DDA Mode',
                    data: topics.map(topic => Number(nonddaAvg[topic] || 0).toFixed(2)),
                    backgroundColor: 'rgba(133, 135, 150, 0.8)',
                    borderColor: '#858796',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, max: 5, title: { display: true, text: 'Level' } } }
        }
    });
}

function averageFromValues(values) {
    if (!values || values.length === 0) return 0;
    const sum = values.reduce((carry, value) => carry + (Number(value) || 0), 0);
    return sum / values.length;
}

function formatTopicAverage(avgByTopic) {
    const values = Object.values(avgByTopic || {}).map(value => Number(value) || 0);
    if (!values.length) {
        return '0.00';
    }
    return averageFromValues(values).toFixed(2);
}

function setAnalysisLoadingState(isLoading) {
    const refreshButton = document.getElementById('btn-refresh-analysis');
    const spinner = document.getElementById('loading-spinner');
    const text = document.getElementById('loading-text');

    if (refreshButton) {
        refreshButton.disabled = isLoading;
        refreshButton.innerHTML = isLoading
            ? '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...'
            : '<i class="fas fa-play me-1"></i> Jalankan Analisis';
    }

    if (spinner) {
        spinner.classList.toggle('d-none', !isLoading);
    }

    if (text) {
        text.textContent = isLoading ? 'Menyusun analisis...' : '';
    }

    const panels = [
        ['nondda-container', 'secondary', 'Memproses Non-DDA...'],
        ['dda-container', 'info', 'Memproses DDA...'],
        ['comparison-container', 'primary', 'Memproses comparison...'],
        ['dda-metrics-container', 'info', 'Memproses metrik DDA...'],
        ['spk-container', 'success', 'Memproses rekap SPK...']
    ];

    panels.forEach(([id, color, label]) => {
        const container = document.getElementById(id);
        if (!container) return;
        if (isLoading) {
            container.innerHTML = `<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-${color} me-2" role="status"></div><span class="text-muted">${label}</span></div>`;
        }
    });
}
function showError(message) {
    const container = document.getElementById('analysis-summary');
    if (container) {
        container.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
}


async function loadDDAMetrics(data) {
    const container = document.getElementById('dda-metrics-container');
    if (!container) return;

    const termId = document.getElementById('term_id')?.value;
    const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);

    if (!termId || !studentIds.length) {
        container.innerHTML = '<div class="alert alert-info">Pilih term dan mahasiswa terlebih dahulu.</div>';
        return;
    }

    try {
        const response = await fetch(`${ddaMetricsUrl}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                term_id: termId,
                student_ids: studentIds
            })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Failed to load DDA metrics');
        }

        renderDDAMetrics(result);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${error.message}</div>`;
    }
}

function renderDDAMetrics(data) {
    const container = document.getElementById('dda-metrics-container');
    if (!container) return;

    const distData = data.difficulty_distribution || {};
    const successData = data.success_rates || {};
    const effectiveness = data.overall_effectiveness || 0;

    let html = `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-robot me-2"></i> DDA Performance Overview</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h4 mb-0 text-info">${data.total_dda_attempts || 0}</div>
                            <small class="text-muted">Total DDA Attempts</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h4 mb-0 ${effectiveness >= 70 ? 'text-success' : 'text-warning'}">${effectiveness}%</div>
                            <small class="text-muted">Overall Effectiveness</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block mb-2">Rekomendasi:</small>
                            <div class="text-sm">${data.recommendation || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-chart-pie me-2"></i> Difficulty Distribution</h6></div>
            <div class="card-body">
                <div class="row g-3">
    `;

    Object.entries(distData).forEach(([diff, stat]) => {
        const color = diff === 'easy' ? 'primary' : (diff === 'medium' ? 'warning' : 'danger');
        html += `
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-capitalize">${diff}</span>
                                <span class="badge bg-${color}">${stat.percentage}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-${color}" style="width: ${stat.percentage}%"></div>
                            </div>
                            <small class="text-muted d-block mt-2">${stat.count} soal</small>
                        </div>
                    </div>
        `;
    });

    html += `
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2"><h6 class="m-0 text-muted"><i class="fas fa-bar-chart me-2"></i> Success Rate by Difficulty</h6></div>
            <div class="card-body"><div class="row"><div class="col-12"><canvas id="dda-success-chart"></canvas></div></div></div>
        </div>
    `;

    container.innerHTML = html;

    // Draw success rates chart
    setTimeout(() => {
        const canvas = document.getElementById('dda-success-chart');
        if (canvas && successData) {
            const labels = Object.keys(successData);
            const rates = Object.values(successData).map(s => s.success_rate);
            
            if (analysisCharts['dda-success-chart']) {
                analysisCharts['dda-success-chart'].destroy();
            }

            analysisCharts['dda-success-chart'] = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Success Rate (%)',
                        data: rates,
                        backgroundColor: ['#36b9cc', '#f6c23e', '#e74c3c'],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }
    }, 100);
}

async function loadSPKRecommendations(data) {
    const container = document.getElementById('spk-container');
    if (!container) return;

    const termId = document.getElementById('term_id')?.value;
    const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);

    if (!termId || !studentIds.length) {
        container.innerHTML = '<div class="alert alert-info">Pilih term dan mahasiswa terlebih dahulu.</div>';
        return;
    }

    try {
        const response = await fetch(`${spkRecommendationsUrl}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                term_id: termId,
                student_ids: studentIds
            })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Failed to load SPK recommendations');
        }

        window.spkRecommendations = result;
        renderEnhancedSPK(result);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${error.message}</div>`;
    }
}

function renderEnhancedSPK(data) {
    const container = document.getElementById('spk-container');
    if (!container) return;

    let html = `<div class="card shadow-sm border-0">
        <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
            <h6 class="m-0 text-muted"><i class="fas fa-user-graduate me-2"></i> Rekap SPK dengan Rekomendasi</h6>
            <button type="button" class="btn btn-sm btn-outline-success" id="btn-export-spk">
                <i class="fas fa-download me-1"></i> Export JSON
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th class="text-center">Level Intervensi</th>
                            <th>Status</th>
                            <th>Topik Lemah</th>
                            <th>Saran Strategi</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    (data.recommendations || []).forEach(rec => {
        const levelColor = rec.intervention_level === 'high' ? 'danger' : (rec.intervention_level === 'medium' ? 'warning' : 'success');
        const weakTopicsStr = rec.weak_topics.map(t => `<span class="badge bg-danger me-1">${t.topic}</span>`).join('');
        
        html += `
                        <tr>
                            <td class="fw-bold">${rec.student_name}</td>
                            <td class="text-center"><span class="badge bg-${levelColor}">${rec.intervention_level.toUpperCase()}</span></td>
                            <td><small>${rec.recommendation}</small></td>
                            <td>${weakTopicsStr || '<span class="text-muted small">Tidak ada</span>'}</td>
                            <td><small>${rec.suggested_strategy}</small></td>
                        </tr>
        `;
    });

    html += `
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;

    container.innerHTML = html;

    // Attach export handler
    document.getElementById('btn-export-spk')?.addEventListener('click', function() {
        const filename = `SPK_Rekomendasi_${new Date().getTime()}.json`;
        const dataStr = JSON.stringify(data, null, 2);
        const blob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        URL.revokeObjectURL(url);
    });
}

function showLoading(show) {
    const spinner = document.getElementById('loading-spinner');
    const text = document.getElementById('loading-text');

    if (show) {
        spinner?.classList.remove('d-none');
        if (text) text.textContent = 'Loading...';
    } else {
        spinner?.classList.add('d-none');
        if (text) text.textContent = '';
    }
}
</script>
@endpush

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .border-left-primary {
        border-left: 4px solid #667eea !important;
    }

    .border-bottom-0 {
        border-bottom: 0 !important;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link:hover {
        color: #495057;
        border-bottom-color: #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #667eea;
        background-color: #f8f9fa;
        border-bottom-color: #667eea !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05) !important;
    }

    .badge {
        font-size: 0.85rem;
        font-weight: 500;
    }

    .card {
        border: none !important;
    }

    .card-header {
        border-bottom: 1px solid #e3e6f0 !important;
    }

    canvas {
        max-height: 400px;
    }
</style>
@endpush









