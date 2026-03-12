<div class="card border-left-secondary bg-light shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="row align-items-center">
            {{-- Session Title & Activities --}}
            <div class="col-12 col-md-8 mb-3 mb-md-0">
                <h6 class="font-weight-bold text-secondary mb-3">
                    <i class="fas fa-bookmark me-2"></i> {{ $session->title }}
                </h6>
                
                <div class="ps-2">
                    @forelse ($session->Related as $activity)
                        <x-box.session-activity-item :activity="$activity" />
                    @empty
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> No activities available
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Grade Section --}}
            <div class="col-12 col-md-4">
                <div class="card border-0 bg-white shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 small font-weight-bold text-gray-700">
                            <i class="fas fa-chart-bar me-1"></i> {{ __('Grade') }}
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        {{-- Progress --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Progress</span>
                                @php
                                    $completed = $session->workout_completed ?? 0;
                                    $total = $session->Related->count();
                                    $progressPercent = $total > 0 ? round(($completed / $total) * 100) : 0;
                                @endphp
                                <span class="small font-weight-bold">{{ $progressPercent }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $completed }}/{{ $total }} completed</div>
                        </div>

                        {{-- Average Score --}}
                        <div>
                            <div class="small text-muted mb-1">Average Score</div>
                            @php
                                $score = $session->workout_score ?? 0;
                                if($score >= 80) {
                                    $scoreColor = 'success';
                                    $scoreLabel = 'Excellent';
                                } elseif($score >= 70) {
                                    $scoreColor = 'info';
                                    $scoreLabel = 'Good';
                                } elseif($score >= 60) {
                                    $scoreColor = 'warning';
                                    $scoreLabel = 'Fair';
                                } elseif($score > 0) {
                                    $scoreColor = 'danger';
                                    $scoreLabel = 'Needs Work';
                                } else {
                                    $scoreColor = 'secondary';
                                    $scoreLabel = 'No Score';
                                }
                            @endphp
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h4 mb-0 font-weight-bold text-{{ $scoreColor }}">
                                    {{ $score > 0 ? $score : '-' }}
                                </span>
                                <span class="badge bg-{{ $scoreColor }}">{{ $scoreLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>