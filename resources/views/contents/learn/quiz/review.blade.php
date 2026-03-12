@extends('layouts.admin')

@section("content")

@can('mentor.list')
@include('contents.learn.mentor.mentor-workout')
@endcan

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                {{-- Card Header --}}
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-gradient-primary text-white">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-check me-2"></i> {{ $activity->title }}
                    </h5>
                    <x-BackButton />
                </div>

                <div class="card-body">
                    {{-- Score & Stats Section --}}
                    <div class="row g-3 mb-4">
                        {{-- Score Card --}}
                        <div class="col-12 col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="small text-muted text-uppercase mb-2">{{ __("Your Score") }}</div>
                                    @php
                                        $score = (int)($workout->score ?? 0);
                                        $scoreColor = $score >= 80 ? 'success' : ($score >= 60 ? 'info' : ($score >= 40 ? 'warning' : 'danger'));
                                    @endphp
                                    <div class="display-4 font-weight-bold text-{{ $scoreColor }} mb-2">{{ $score }}</div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $scoreColor }}" role="progressbar" style="width: {{ $score }}%"></div>
                                    </div>
                                    <div class="mt-2">
                                        @if($score >= 80)
                                            <span class="badge bg-success"><i class="fa fa-star"></i> Excellent</span>
                                        @elseif($score >= 60)
                                            <span class="badge bg-info"><i class="fa fa-thumbs-up"></i> Good</span>
                                        @elseif($score >= 40)
                                            <span class="badge bg-warning text-dark"><i class="fa fa-exclamation"></i> Need Improvement</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fa fa-times"></i> Keep Practicing</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quiz Settings Cards --}}
                        <div class="col-12 col-md-8">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light py-2">
                                    <h6 class="m-0 text-muted"><i class="fas fa-cog me-2"></i> Quiz Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border rounded p-3">
                                                <div class="h3 mb-0 text-primary">{{ (int)($activity->random_question ?? 0) }}</div>
                                                <div class="small text-muted">Random Questions</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border rounded p-3">
                                                <div class="h3 mb-0 text-info">{{ (int)($activity->questions->count() ?? 0) }}</div>
                                                <div class="small text-muted">Total Questions</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border rounded p-3">
                                                @if((int)($activity->is_shuffle ?? 0) === 1)
                                                    <div class="h3 mb-0 text-success"><i class="fa fa-check-circle"></i></div>
                                                @else
                                                    <div class="h3 mb-0 text-secondary"><i class="fa fa-times-circle"></i></div>
                                                @endif
                                                <div class="small text-muted">Shuffle</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Difficulty Legend --}}
                    <div class="alert alert-light border mb-4">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <strong>Difficulty Legend:</strong>
                        <span class="badge bg-success ms-2">Easy = 1</span>
                        <span class="badge bg-warning text-dark ms-1">Medium = 2</span>
                        <span class="badge bg-danger ms-1">Hard = 3</span>
                    </div>

                    {{-- Questions & Answers (Livewire) --}}
                    <div class="mb-4">
                        @livewire('activity.result', [
                            'activity' => $activity,
                            'participant' => $participant,
                            'workout' => $workout
                        ])
                    </div>

                    {{-- DDA Card --}}
                    <div class="mb-4">
                        @include('contents.learn.quiz._dda_card')
                    </div>

                    {{-- Tabbed Analysis Panel --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 text-muted"><i class="fas fa-chart-line me-2"></i> Learning Analysis</h6>
                        </div>
                        <div class="card-body p-0">
                            {{-- Nav Tabs --}}
                            <ul class="nav nav-tabs border-bottom-0" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-nondda" data-bs-toggle="tab" data-bs-target="#content-nondda" type="button" role="tab" aria-controls="content-nondda" aria-selected="true">
                                        <i class="fas fa-ban me-1"></i> Non-DDA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-dda" data-bs-toggle="tab" data-bs-target="#content-dda" type="button" role="tab" aria-controls="content-dda" aria-selected="false">
                                        <i class="fas fa-robot me-1"></i> DDA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-combined" data-bs-toggle="tab" data-bs-target="#content-combined" type="button" role="tab" aria-controls="content-combined" aria-selected="false">
                                        <i class="fas fa-balance-scale me-1"></i> Comparison
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Content --}}
                            <div class="tab-content p-4">
                                {{-- Non-DDA Tab --}}
                                <div class="tab-pane fade show active" id="content-nondda" role="tabpanel" aria-labelledby="tab-nondda">
                                    <div id="nondda-analysis">
                                        <div id="nondda-loading" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                                            <span class="text-muted">Loading Non-DDA analysis...</span>
                                        </div>
                                        <div id="nondda-error" class="alert alert-danger d-none"></div>
                                        <div id="nondda-summary"></div>
                                        <div id="nondda-graphs" class="row g-3"></div>
                                    </div>
                                </div>

                                {{-- DDA Tab --}}
                                <div class="tab-pane fade" id="content-dda" role="tabpanel" aria-labelledby="tab-dda">
                                    <div id="dda-analysis">
                                        <div id="dda-loading" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                                            <span class="text-muted">Loading DDA analysis...</span>
                                        </div>
                                        <div id="dda-error" class="alert alert-danger d-none"></div>
                                        <div id="dda-summary"></div>
                                        <div id="dda-graphs" class="row g-3"></div>
                                    </div>
                                </div>

                                {{-- Combined Tab --}}
                                <div class="tab-pane fade" id="content-combined" role="tabpanel" aria-labelledby="tab-combined">
                                    <div id="combined-analysis">
                                        <div id="combined-loading" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                            <span class="text-muted">Loading comparison...</span>
                                        </div>
                                        <div id="combined-error" class="alert alert-danger d-none"></div>
                                        <div id="combined-summary"></div>
                                        <div id="combined-graphs" class="row g-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Restart History --}}
                    <div class="card shadow-sm border-left-secondary mb-4">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-muted"><i class="fas fa-history me-2"></i> Restart History</h6>
                            @if(optional($workout->RestartLogs)->count() > 0)
                                <span class="badge bg-secondary">{{ $workout->RestartLogs()->count() }} records</span>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if(optional($workout->RestartLogs)->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">#</th>
                                                <th>Type</th>
                                                <th>Date/Time</th>
                                                <th>Difficulty</th>
                                                <th>Previous Score</th>
                                                <th>Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workout->RestartLogs()->latest()->limit(5)->get() as $idx => $log)
                                                <tr>
                                                    <td class="ps-3">{{ $idx + 1 }}</td>
                                                    <td>
                                                        @if($log->used_dda ?? true)
                                                            <span class="badge bg-info text-white"><i class="fas fa-robot me-1"></i> DDA</span>
                                                        @else
                                                            <span class="badge bg-secondary text-white"><i class="fas fa-ban me-1"></i> Non-DDA</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <i class="far fa-clock text-muted me-1"></i>
                                                        {{ $log->created_at->format("d M Y, H:i") }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $diff = $log->dda_difficulty ?? null;
                                                            if($diff) {
                                                                $diffColor = $diff === 'easy' ? 'success' : ($diff === 'medium' ? 'warning' : ($diff === 'hard' ? 'danger' : 'secondary'));
                                                                $diffLabel = ucfirst($diff);
                                                            } else {
                                                                $diffColor = 'secondary';
                                                                $diffLabel = 'No DDA';
                                                            }
                                                        @endphp
                                                        <span class="badge text-white bg-{{ $diffColor }}">{{ $diffLabel }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $log->previous_score ?? '-' }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">{{ is_array($log->payload) ? count($log->payload) : 0 }} items</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">No restart history available.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allRestartLogs = {!! json_encode(
        (isset($workout) && $workout->RestartLogs()->count() > 0)
            ? $workout->RestartLogs()->latest()->limit(50)->get()->map(function($l){
                $p = is_array($l->payload) ? $l->payload : (is_string($l->payload) ? json_decode($l->payload, true) : []);
                return [
                    'created_at' => optional($l->created_at)->toDateTimeString() ?: now()->toDateTimeString(),
                    'dda_difficulty' => $l->dda_difficulty,
                    'previous_score' => $l->previous_score !== null ? (float)$l->previous_score : null,
                    'payload' => is_array($p) ? $p : [],
                    'used_dda' => $l->used_dda ?? true,
                ];
            })->toArray()
            : []
    ) !!};

    const nonddalogs = allRestartLogs.filter(l => !l.used_dda);
    const ddalogs = allRestartLogs.filter(l => l.used_dda);

    function loadAnalysis(logs, prefix) {
        if(!logs || logs.length === 0){
            document.getElementById(prefix + '-loading').innerHTML = '<div class="text-muted"><i class="fas fa-info-circle me-1"></i> No data to analyze.</div>';
            return;
        }

        const payload = {
            workout_id: {{ $workout->id ?? 'null' }},
            restart_logs: logs
        };

        fetch('http://127.0.0.1:8001/analyze', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(res => {
            if(!res.ok) throw new Error('Server returned ' + res.status);
            return res.json();
        }).then(data => {
            document.getElementById(prefix + '-loading').classList.add('d-none');
            const summary = data.summary || {};
            const graphs = data.graphs || {};

            const sumEl = document.getElementById(prefix + '-summary');
            let html = '<div class="row g-2 mb-3">';
            Object.keys(summary).forEach(k => {
                let v = summary[k];
                try { v = (typeof v === 'object') ? JSON.stringify(v) : v; } catch(e){}
                html += '<div class="col-6 col-md-3"><div class="border rounded p-2 text-center bg-light"><div class="small text-muted">' + k.replace(/_/g, ' ') + '</div><div class="fw-bold">' + v + '</div></div></div>';
            });
            html += '</div>';
            sumEl.innerHTML = html;

            const graphsEl = document.getElementById(prefix + '-graphs');
            Object.entries(graphs).forEach(([k, v]) => {
                if(v){
                    const col = document.createElement('div');
                    col.className = 'col-12 col-md-6';
                    const card = document.createElement('div');
                    card.className = 'card h-100';
                    const header = document.createElement('div');
                    header.className = 'card-header py-2 bg-light';
                    header.innerHTML = '<small class="text-muted">' + k.replace(/_/g, ' ').toUpperCase() + '</small>';
                    const body = document.createElement('div');
                    body.className = 'card-body text-center p-2';
                    const img = document.createElement('img');
                    img.src = v;
                    img.alt = k;
                    img.className = 'img-fluid';
                    body.appendChild(img);
                    card.appendChild(header);
                    card.appendChild(body);
                    col.appendChild(card);
                    graphsEl.appendChild(col);
                }
            });
        }).catch(err => {
            document.getElementById(prefix + '-loading').classList.add('d-none');
            const errEl = document.getElementById(prefix + '-error');
            errEl.classList.remove('d-none');
            errEl.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Analysis failed: ' + err.message;
        });
    }

    // Load Non-DDA
    loadAnalysis(nonddalogs, 'nondda');

    // Load DDA
    loadAnalysis(ddalogs, 'dda');

    // Load Combined (all logs)
    loadAnalysis(allRestartLogs, 'combined');
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.5rem 1rem;
}
.nav-tabs .nav-link.active {
    color: #4e73df;
    border-bottom-color: #4e73df;
    background-color: transparent;
}
.nav-tabs .nav-link:hover {
    border-bottom-color: #4e73df;
    color: #4e73df;
}
.border-left-secondary {
    border-left: 4px solid #858796 !important;
}
</style>

@endsection
