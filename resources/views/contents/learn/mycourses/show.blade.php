@extends('layouts.admin')

@section("content")

<div class="container-fluid">
    {{-- Page Header --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between bg-gradient-primary text-white">
            <div>
                <h5 class="m-0 font-weight-bold">
                    <i class="fas fa-book-open me-2"></i> {{ $participant->Term->Course->title ?? 'Course' }} - <span class="text-white-40">{{ $participant->Term->title ?? 'Term' }}</span>
                </h5>

            </div>
            <div class="d-flex align-items-center" style="gap: 10px;">
                @can('mentor.list')
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header">{{ __('Management') }}</div>
                        <x-modules.mentor-comments.new-comment :userId="$participant->User->id" :activableType="'App\Models\Term'" :activableId="$participant->id" />
                    </div>
                </div>
                @endcan
                <x-BackButton />
            </div>
        </div>
    </div>

    {{-- Profile & Progress Section --}}
    <div class="row mb-4">
        <div class="col-12 col-lg-5 mb-3 mb-lg-0">
            <x-box.profile-top-header :user="$participant->User" :activabel_id="$participant->id" :activable_type="'App\Models\Term'" />
        </div>
        <div class="col-12 col-lg-7">
            {{-- Progress Overview Card --}}
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 text-muted"><i class="fas fa-chart-pie me-2"></i> Progress Overview</h6>
                </div>
                <div class="card-body">
                    @php
                        $sessions = $participant->Term->Sessions ?? collect();
                        $totalSessions = $sessions->count();
                        $completedActivities = 0;
                        $totalActivities = 0;
                        $totalScore = 0;
                        $scoreCount = 0;

                        foreach($sessions as $session) {
                            $activities = $session->Activities ?? collect();
                            foreach($activities as $activity) {
                                $totalActivities++;
                                $workout = optional($participant->Workout)->where('activity_id', $activity->id)->first();
                                if($workout && $workout->is_completed) {
                                    $completedActivities++;
                                    if($workout->score !== null) {
                                        $totalScore += $workout->score;
                                        $scoreCount++;
                                    }
                                }
                            }
                        }
                        $progress = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100) : 0;
                        $avgScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
                    @endphp

                    <div class="row text-center g-3">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="h3 mb-0 text-primary">{{ $totalSessions }}</div>
                                <div class="small text-muted">Sessions</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="h3 mb-0 text-info">{{ $totalActivities }}</div>
                                <div class="small text-muted">Activities</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="h3 mb-0 text-success">{{ $completedActivities }}</div>
                                <div class="small text-muted">Completed</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                @php
                                    $scoreColor = $avgScore >= 80 ? 'success' : ($avgScore >= 60 ? 'info' : ($avgScore >= 40 ? 'warning' : 'danger'));
                                @endphp
                                <div class="h3 mb-0 text-{{ $avgScore > 0 ? $scoreColor : 'secondary' }}">{{ $avgScore > 0 ? $avgScore : '-' }}</div>
                                <div class="small text-muted">Avg Score</div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Overall Progress</span>
                            <span class="small fw-bold">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Box --}}
    <x-box.profile-review-box />

    {{-- Sessions Section --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-gray-700">
                <i class="fas fa-layer-group me-2"></i> {{ __('Sessions') }}
            </h6>
            <span class="badge bg-primary">{{ ($participant->Term->Sessions ?? collect())->count() }} sessions</span>
        </div>
        <div class="card-body p-0">
            @forelse (($participant->Term->Sessions ?? collect()) as $index => $session)
                <div class="{{ !$loop->last ? 'border-bottom' : '' }}">
                    <x-box.session-item-for-student :session="$session" :participant="$participant" />
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-secondary mb-3"></i>
                    <h5 class="text-muted">{{ __('No Sessions Available') }}</h5>
                    <p class="text-muted small">Sessions will appear here once they are added to this term.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}
</style>

@endsection
