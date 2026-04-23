@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header Card --}}
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
                        Data aggregated from all answers and restart logs per student per topic.
                    </p>
                </div>
            </div>

            {{-- Legend Card --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-palette me-2"></i> Level Legend</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-auto">
                            <span class="badge bg-success p-2"><i class="fas fa-star me-1"></i> Level 4 (80-100%)</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info p-2"><i class="fas fa-check-circle me-1"></i> Level 3 (60-79%)</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-warning text-dark p-2"><i class="fas fa-exclamation-circle me-1"></i> Level 2 (40-59%)</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-secondary p-2"><i class="fas fa-circle me-1"></i> Level 1 (1-39%)</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-dark border p-2"><i class="fas fa-times me-1"></i> Level 0 (0%)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabbed Analysis --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-chart-line me-2"></i> Analysis Modes</h6>
                </div>
                <div class="card-body p-0">
                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs border-bottom-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-nondda" data-bs-toggle="tab" data-bs-target="#content-nondda" type="button" role="tab" aria-controls="content-nondda" aria-selected="true">
                                <i class="fas fa-ban me-1"></i> Non-DDA Mode
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-dda" data-bs-toggle="tab" data-bs-target="#content-dda" type="button" role="tab" aria-controls="content-dda" aria-selected="false">
                                <i class="fas fa-robot me-1"></i> DDA Mode
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-comparison" data-bs-toggle="tab" data-bs-target="#content-comparison" type="button" role="tab" aria-controls="content-comparison" aria-selected="false">
                                <i class="fas fa-balance-scale me-1"></i> Comparison
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-quiz-comparison" data-bs-toggle="tab" data-bs-target="#content-quiz-comparison" type="button" role="tab" aria-controls="content-quiz-comparison" aria-selected="false">
                                <i class="fas fa-check-square me-1"></i> Quiz vs Overall
                            </button>
                        </li>
                        <li class="nav-item ms-auto" role="presentation">
                            <span class="nav-link disabled text-muted small">
                                <i class="fas fa-spinner fa-spin me-1 d-none" id="loading-spinner"></i>
                                <span id="loading-text"></span>
                            </span>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content p-4">
                        {{-- Non-DDA Tab --}}
                        <div class="tab-pane fade show active" id="content-nondda" role="tabpanel" aria-labelledby="tab-nondda">
                            <div id="nondda-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                                    <span class="text-muted">Loading Non-DDA analysis...</span>
                                </div>
                            </div>
                        </div>

                        {{-- DDA Tab --}}
                        <div class="tab-pane fade" id="content-dda" role="tabpanel" aria-labelledby="tab-dda">
                            <div id="dda-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                                    <span class="text-muted">Loading DDA analysis...</span>
                                </div>
                            </div>
                        </div>

                        {{-- Comparison Tab --}}
                        <div class="tab-pane fade" id="content-comparison" role="tabpanel" aria-labelledby="tab-comparison">
                            <div id="comparison-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted">Loading comparison analysis...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Raw Data Export (For debugging/verification) --}}
            <div class="card shadow-sm mt-4 d-none" id="debug-section">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-bug me-2"></i> Debug Data</h6>
                </div>
                <div class="card-body">
                    <pre id="debug-data" class="mb-0"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const comparisonData = await loadComparisonData();

    if (!comparisonData) {
        showError('Failed to load comparison data');
        return;
    }

    // Load all three tabs
    await loadNonDDAAnalysis(comparisonData);
    await loadDDAAnalysis(comparisonData);
    await loadComparisonAnalysis(comparisonData);

    console.log('Comparison Data:', comparisonData);
});

/**
 * Load comparison data from API
 */
async function loadComparisonData() {
    try {
        showLoading(true);
        const response = await fetch('{{ route("api.topic-comparison") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('API returned status ' + response.status);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Unknown error');
        }

        showLoading(false);
        return data;

    } catch (error) {
        console.error('Error loading comparison data:', error);
        showError('Unable to load comparison data: ' + error.message);
        return null;
    }
}

/**
 * Load and display Non-DDA analysis
 */
async function loadNonDDAAnalysis(data) {
    const container = document.getElementById('nondda-container');

    if (!data.nondda_data || data.nondda_data.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No Non-DDA data available yet.
            </div>
        `;
        return;
    }

    let html = '';

    // Statistics Summary
    html += `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> Non-DDA Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-secondary">
                                <i class="fas fa-users me-2"></i>${data.stats.nondda_count}
                            </div>
                            <small class="text-muted">Students with Non-DDA data</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-secondary">
                                <i class="fas fa-bookmark me-2"></i>${data.topics.length}
                            </div>
                            <small class="text-muted">Topics</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-secondary">
                                <i class="fas fa-chart-line me-2"></i>${(Object.values(data.stats.nondda_avg).reduce((a, b) => a + b, 0) / data.topics.length).toFixed(2)}
                            </div>
                            <small class="text-muted">Average Level</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Heatmap
    html += `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-th me-2"></i> Heatmap (Non-DDA)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    ${generateHeatmap(data.nondda_data, data.topics)}
                </div>
            </div>
        </div>
    `;

    // Average Levels Chart
    html += `
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> Average Levels by Topic</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <canvas id="nondda-avg-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;

    // Draw chart
    setTimeout(() => {
        drawAverageChart('nondda-avg-chart', data.stats.nondda_avg, data.topics, 'Non-DDA Average Levels', '#858796');
    }, 100);
}

/**
 * Load and display DDA analysis
 */
async function loadDDAAnalysis(data) {
    const container = document.getElementById('dda-container');

    if (!data.dda_data || data.dda_data.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No DDA data available yet.
            </div>
        `;
        return;
    }

    let html = '';

    // Statistics Summary
    html += `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> DDA Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-info">
                                <i class="fas fa-users me-2"></i>${data.stats.dda_count}
                            </div>
                            <small class="text-muted">Students with DDA data</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-info">
                                <i class="fas fa-bookmark me-2"></i>${data.topics.length}
                            </div>
                            <small class="text-muted">Topics</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <div class="h5 mb-0 text-info">
                                <i class="fas fa-chart-line me-2"></i>${(Object.values(data.stats.dda_avg).reduce((a, b) => a + b, 0) / data.topics.length).toFixed(2)}
                            </div>
                            <small class="text-muted">Average Level</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Heatmap
    html += `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-th me-2"></i> Heatmap (DDA)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    ${generateHeatmap(data.dda_data, data.topics)}
                </div>
            </div>
        </div>
    `;

    // Average Levels Chart
    html += `
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> Average Levels by Topic</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <canvas id="dda-avg-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;

    // Draw chart
    setTimeout(() => {
        drawAverageChart('dda-avg-chart', data.stats.dda_avg, data.topics, 'DDA Average Levels', '#36b9cc');
    }, 100);
}

/**
 * Load and display comparison analysis
 */
async function loadComparisonAnalysis(data) {
    const container = document.getElementById('comparison-container');

    let html = '';

    // Comparison Summary
    html += `
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-table me-2"></i> Comparison Summary</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2"><i class="fas fa-ban me-2"></i> Non-DDA Mode</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Students:</small>
                                    <div class="fw-bold">${data.stats.nondda_count}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Avg Level:</small>
                                    <div class="fw-bold text-secondary">${(Object.values(data.stats.nondda_avg).reduce((a, b) => a + b, 0) / data.topics.length).toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2"><i class="fas fa-robot me-2"></i> DDA Mode</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Students:</small>
                                    <div class="fw-bold">${data.stats.dda_count}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Avg Level:</small>
                                    <div class="fw-bold text-info">${(Object.values(data.stats.dda_avg).reduce((a, b) => a + b, 0) / data.topics.length).toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Side-by-side comparison chart
    html += `
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 text-muted"><i class="fas fa-chart-bar me-2"></i> DDA vs Non-DDA by Topic</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <canvas id="comparison-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;

    // Draw comparison chart
    setTimeout(() => {
        drawComparisonChart('comparison-chart', data.stats.dda_avg, data.stats.nondda_avg, data.topics);
    }, 100);
}

/**
 * Generate HTML heatmap table
 */
function generateHeatmap(studentData, topics) {
    if (!studentData || studentData.length === 0) {
        return '<div class="alert alert-info mb-0">No data available</div>';
    }

    let html = '<table class="table table-striped table-hover mb-0 small">';
    html += '<thead class="table-light"><tr><th>Student</th>';

    // Add topic headers
    topics.forEach(topic => {
        html += `<th class="text-center" title="${topic}">${topic.substring(0, 10)}</th>`;
    });

    html += '</tr></thead><tbody>';

    // Add student rows
    studentData.forEach(student => {
        html += `<tr><td class="fw-bold">${student.student_name}</td>`;

        topics.forEach(topic => {
            const level = student.levels[topic] || 0;
            const color = getLevelColor(level);
            html += `<td class="text-center"><span class="badge ${color} p-2">${level}</span></td>`;
        });

        html += '</tr>';
    });

    html += '</tbody></table>';
    return html;
}

/**
 * Get color class for level
 */
function getLevelColor(level) {
    switch(parseInt(level)) {
        case 4: return 'bg-success';
        case 3: return 'bg-info';
        case 2: return 'bg-warning text-dark';
        case 1: return 'bg-secondary';
        default: return 'bg-light text-dark border';
    }
}

/**
 * Draw average levels bar chart
 */
function drawAverageChart(canvasId, avgData, topics, title, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    const sortedTopics = [...topics].sort((a, b) => (avgData[b] || 0) - (avgData[a] || 0));
    const sortedData = sortedTopics.map(t => (avgData[t] || 0).toFixed(2));

    new Chart(ctx, {
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
            indexAxis: 'x',
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { callback: function(value) { return value; } }
                }
            }
        }
    });
}

/**
 * Draw side-by-side comparison chart
 */
function drawComparisonChart(canvasId, ddaAvg, nonddaAvg, topics) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topics,
            datasets: [
                {
                    label: 'DDA Mode',
                    data: topics.map(t => (ddaAvg[t] || 0).toFixed(2)),
                    backgroundColor: 'rgba(54, 185, 204, 0.8)',
                    borderColor: '#36b9cc',
                    borderWidth: 1
                },
                {
                    label: 'Non-DDA Mode',
                    data: topics.map(t => (nonddaAvg[t] || 0).toFixed(2)),
                    backgroundColor: 'rgba(133, 135, 150, 0.8)',
                    borderColor: '#858796',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    title: { display: true, text: 'Level' }
                }
            }
        }
    });
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.getElementById('nondda-container');
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

/**
 * Show/hide loading indicator
 */
function showLoading(show) {
    const spinner = document.getElementById('loading-spinner');
    const text = document.getElementById('loading-text');

    if (show) {
        spinner?.classList.remove('d-none');
        text.textContent = 'Loading...';
    } else {
        spinner?.classList.add('d-none');
        text.textContent = '';
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


 
 @ s e c t i o n (  
 ' j s '  
 )  
 < s c r i p t   s r c = " h t t p s : / / c d n . j s d e l i v r . n e t / n p m / c h a r t . j s @ 3 . 9 . 1 / d i s t / c h a r t . m i n . j s " > < / s c r i p t >  
 < s c r i p t >  
 // Quiz Comparison Variables
let quizComparisonCharts = {};

// When Quiz Comparison tab is clicked
document.getElementById('tab-quiz-comparison')?.addEventListener('click', function() {
    document.getElementById('quiz-selector-card').classList.remove('d-none');
    loadAvailableQuizzes();
});

// Load available quizzes
function loadAvailableQuizzes() {
    const termId = document.getElementById('term_id')?.value;
    if (!termId) return;
    fetch(`{{ route('api.topic-comparison.quizzes') }}?term_id=${termId}`)
        .then(r => r.json())
        .then(quizzes => {
            const selector = document.getElementById('quiz_selector');
            selector.innerHTML = '<option value="">-- Select a Quiz --</option>';
            quizzes.forEach(q => {
                const opt = document.createElement('option');
                opt.value = q.id;
                opt.textContent = q.title;
                selector.appendChild(opt);
            });
        })
        .catch(e => console.error('Failed to load quizzes:', e));
}

// Enable button when quiz selected
document.getElementById('quiz_selector')?.addEventListener('change', function() {
    document.getElementById('btn-load-quiz-comparison').disabled = !this.value;
});

// Load quiz comparison
document.getElementById('btn-load-quiz-comparison')?.addEventListener('click', async function() {
    const termId = document.getElementById('term_id').value;
    const sessionableId = document.getElementById('quiz_selector').value;
    const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    if (!studentIds.length) {
        alert('Please select at least one student');
        return;
    }
    document.getElementById('quiz-comparison-container').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary me-2" role="status"></div>
            <span class="text-muted">Loading quiz comparison...</span>
        </div>
    `;
    try {
        const response = await fetch('{{ route('analytics.topic-levels-quiz-comparison') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                term_id: termId,
                sessionable_id: sessionableId,
                student_ids: studentIds
            })
        });
        const data = await response.json();
        if (data.success) {
            renderQuizComparison(data);
        } else {
            document.getElementById('quiz-comparison-container').innerHTML = 
                `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${data.error || 'Failed to load'}</div>`;
        }
    } catch (e) {
        document.getElementById('quiz-comparison-container').innerHTML = 
            `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${e.message}</div>`;
    }
});

// Render quiz comparison data
function renderQuizComparison(data) {
    const container = document.getElementById('quiz-comparison-container');
    let html = `
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="m-0"><strong>${data.quiz_name}</strong> vs Overall Performance</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="mb-3 text-center"><i class="fas fa-book me-2"></i>${data.quiz_name}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Student</th>
                                        ${data.topics.map(t => `<th class="text-center" style="width: 60px;">${t.substring(0, 3)}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.quiz_data.map(student => `
                                        <tr>
                                            <td class="fw-bold">${student.name}</td>
                                            ${data.topics.map(topic => {
                                                const level = student.levels[topic] ?? 0;
                                                const bgClass = ['bg-secondary', 'bg-warning', 'bg-warning', 'bg-info', 'bg-success'][level] || 'bg-secondary';
                                                return `<td class="text-center text-white ${bgClass}"><strong>L${level}</strong></td>`;
                                            }).join('')}
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-center"><i class="fas fa-chart-line me-2"></i>Overall Performance</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Student</th>
                                        ${data.topics.map(t => `<th class="text-center" style="width: 60px;">${t.substring(0, 3)}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.overall_data.map(student => `
                                        <tr>
                                            <td class="fw-bold">${student.name}</td>
                                            ${data.topics.map(topic => {
                                                const level = student.levels[topic] ?? 0;
                                                const bgClass = ['bg-secondary', 'bg-warning', 'bg-warning', 'bg-info', 'bg-success'][level] || 'bg-secondary';
                                                return `<td class="text-center text-white ${bgClass}"><strong>L${level}</strong></td>`;
                                            }).join('')}
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-light"><small>${data.quiz_name} - Average Level</small></div>
                            <div class="card-body"><canvas id="quiz-avg-chart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-light"><small>Overall - Average Level</small></div>
                            <div class="card-body"><canvas id="overall-avg-chart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.innerHTML = html;
    renderQuizAvgChart(data);
    renderOverallAvgChart(data);
}

function renderQuizAvgChart(data) {
    const ctx = document.getElementById('quiz-avg-chart')?.getContext('2d');
    if (!ctx) return;
    if (quizComparisonCharts['quiz-avg']) quizComparisonCharts['quiz-avg'].destroy();
    quizComparisonCharts['quiz-avg'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.topics,
            datasets: [{
                label: 'Average Level',
                data: data.topics.map(t => data.stats.quiz.avg_level_by_topic[t] ?? 0),
                backgroundColor: '#4e73df',
            }]
        },
        options: { responsive: true, scales: { y: { max: 5, beginAtZero: true } } }
    });
}

function renderOverallAvgChart(data) {
    const ctx = document.getElementById('overall-avg-chart')?.getContext('2d');
    if (!ctx) return;
    if (quizComparisonCharts['overall-avg']) quizComparisonCharts['overall-avg'].destroy();
    quizComparisonCharts['overall-avg'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.topics,
            datasets: [{
                label: 'Average Level',
                data: data.topics.map(t => data.stats.overall.avg_level_by_topic[t] ?? 0),
                backgroundColor: '#1cc88a',
            }]
        },
        options: { responsive: true, scales: { y: { max: 5, beginAtZero: true } } }
    });
}
</script>
@endsection
