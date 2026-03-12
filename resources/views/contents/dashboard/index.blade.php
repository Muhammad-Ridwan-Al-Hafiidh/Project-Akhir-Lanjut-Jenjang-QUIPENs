@extends('layouts.admin')

@section("content")

<div class="container-fluid">
    {{-- Admin Section --}}
    @can('course.index')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i> @lang('Manage Course')</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('department.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/department.jpg') }}" alt="{{ __('Department') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Department') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('course.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/course.jpg') }}" alt="{{ __('Course') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Course') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('term.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/term.jpg') }}" alt="{{ __('terms') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Terms') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('session.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/session.jpg') }}" alt="{{ __('Sessions') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Sessions') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow-sm">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i> @lang('Manage Assessment')</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('quiz.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/quiz.png') }}" alt="{{ __('Quiz') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Quiz') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('question.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/question.png') }}" alt="{{ __('Question') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Question') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- Plugins Section --}}
    @canany(['rubric.index', 'feedback.index', 'file.index', 'document.index', 'badges.index'])
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-info shadow-sm">
                <div class="card-header bg-gradient-info text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i> @lang('Manage Plugins')</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @can('rubric.index')
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('rubric.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/rubric.png') }}" alt="{{ __('Rubric') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Rubric') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                        @can('feedback.index')
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('feedback.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/feedback.png') }}" alt="{{ __('Feedback') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Feedback') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                        @can('file.index')
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('file.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/file.png') }}" alt="{{ __('Files') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Files') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                        @can('document.index')
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('document.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/document.png') }}" alt="{{ __('Document') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Document') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                        @can('badges.index')
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('badges.index') }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                                    <div class="card-body text-center p-3">
                                        <img class="img-fluid mb-2" style="max-height: 60px;" src="{{ asset('img/admin/menu/badge.png') }}" alt="{{ __('Badges') }}">
                                        <div class="small font-weight-bold text-gray-800 text-uppercase">{{ __('Badges') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcanany

    {{-- Profile & Leaderboard Section --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-5">
            <x-box.profile-box :user="$user" />
        </div>
        <div class="col-12 col-lg-7">
            <x-box.leader-board.top-learner-score :user="$user" />
        </div>
    </div>

    {{-- Panel Analisa Statistik User - Full Width --}}
    @php
        $studentUserIds = \DB::table('term_user')->where('role_id', 4)->pluck('user_id')->unique();

        $studentScores = \App\Models\User::whereIn('id', $studentUserIds)
            ->with(['Participants.Workout' => function($q) {
                $q->where('is_completed', 1)->whereNotNull('score');
            }])
            ->get()
            ->map(function($user) {
                $allScores = $user->Participants
                    ->flatMap(fn($p) => $p->Workout)
                    ->pluck('score')
                    ->filter(fn($s) => $s !== null);

                return [
                    'user' => $user,
                    'avg_score' => $allScores->count() > 0 ? round($allScores->avg(), 1) : 0,
                    'total_quiz' => $allScores->count(),
                    'max_score' => $allScores->count() > 0 ? $allScores->max() : 0,
                    'min_score' => $allScores->count() > 0 ? $allScores->min() : 0,
                ];
            })
            ->filter(fn($item) => $item['total_quiz'] > 0)
            ->sortByDesc('avg_score')
            ->values();

        $currentUser = auth()->user();
        $myStats = null;
        $myRank = null;
        if ($currentUser) {
            foreach ($studentScores as $idx => $item) {
                if ($item['user']->id === $currentUser->id) {
                    $myStats = $item;
                    $myRank = $idx + 1;
                    break;
                }
            }
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 text-white"><i class="fa fa-chart-bar me-2"></i> Analisa Statistik Saya</h5>
                </div>
                <div class="card-body">
                    @if($myStats)
                        {{-- Ranking User --}}
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-primary text-white rounded-3">
                            <span class="h6 mb-0"><i class="fa fa-medal me-2"></i> Ranking Anda</span>
                            <span class="h3 mb-0 font-weight-bold">#{{ $myRank }} <small class="text-white-50 fs-6">/ {{ $studentScores->count() }} mahasiswa</small></span>
                        </div>

                        <div class="row g-3">
                            {{-- Rata-rata --}}
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center h-100 bg-light">
                                    <div class="small text-muted mb-1">Rata-rata</div>
                                    <div class="h3 mb-0 text-primary fw-bold">{{ $myStats['avg_score'] }}</div>
                                </div>
                            </div>
                            {{-- Total Quiz --}}
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center h-100 bg-light">
                                    <div class="small text-muted mb-1">Total Quiz</div>
                                    <div class="h3 mb-0 text-info fw-bold">{{ $myStats['total_quiz'] }}</div>
                                </div>
                            </div>
                            {{-- Nilai Tertinggi --}}
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center h-100 bg-light">
                                    <div class="small text-muted mb-1">Nilai Tertinggi</div>
                                    <div class="h3 mb-0 text-success fw-bold">{{ $myStats['max_score'] }}</div>
                                </div>
                            </div>
                            {{-- Nilai Terendah --}}
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center h-100 bg-light">
                                    <div class="small text-muted mb-1">Nilai Terendah</div>
                                    <div class="h3 mb-0 text-danger fw-bold">{{ $myStats['min_score'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Performa Rata-rata</span>
                                <span class="fw-bold">{{ $myStats['avg_score'] }}%</span>
                            </div>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated @if($myStats['avg_score'] >= 80) bg-success @elseif($myStats['avg_score'] >= 60) bg-info @elseif($myStats['avg_score'] >= 40) bg-warning @else bg-danger @endif" role="progressbar" style="width: {{ $myStats['avg_score'] }}%">
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                @if($myStats['avg_score'] >= 80)
                                    <span class="badge bg-success px-3 py-2"><i class="fa fa-star me-1"></i> Excellent! Pertahankan prestasimu!</span>
                                @elseif($myStats['avg_score'] >= 60)
                                    <span class="badge bg-info px-3 py-2"><i class="fa fa-thumbs-up me-1"></i> Good job! Terus tingkatkan!</span>
                                @elseif($myStats['avg_score'] >= 40)
                                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fa fa-exclamation-triangle me-1"></i> Perlu ditingkatkan</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2"><i class="fa fa-times me-1"></i> Butuh latihan lebih banyak</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-info-circle fa-3x mb-3 text-secondary"></i>
                            <p class="mb-0 h6">Belum ada data quiz.</p>
                            <p class="small text-muted">Mulai kerjakan quiz untuk melihat statistik Anda!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    }
</style>

@endsection
