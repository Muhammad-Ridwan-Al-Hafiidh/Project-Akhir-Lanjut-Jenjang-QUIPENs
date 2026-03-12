@php
    // Ambil semua user student (role_id = 4) dari term_user dan hitung rata-rata nilai quiz
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
    
    $topTen = $studentScores->take(10);
@endphp

<div class="card bg-primary">
    <div class="card-header bg-primary text-white border-0 text-center">
        <h5 class="mb-1"><i class="fa fa-trophy me-3"></i> Leaderboard</h5>
        <small>Ranking Rata-rata Nilai Quiz</small>
    </div>
    <div class="card-body">
        @forelse($topTen as $index => $item)
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center flex-shrink-0 me-3">
                <div class="me-2 text-center" style="width: 30px;">
                    @if($index === 0)
                        <i class="fa fa-crown text-warning fa-lg"></i>
                    @elseif($index === 1)
                        <span class="badge bg-light text-dark">2</span>
                    @elseif($index === 2)
                        <span class="badge bg-warning text-dark">3</span>
                    @else
                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                    @endif
                </div>
                <div class="avatar avatar-xl me-3 bg-gray-200">
                    <img class="avatar-img img-fluid" src="{{ URL::to('img/profiles/' . (($item['user']->id % 12) + 1) . '.jpg') }}" alt="">
                </div>
                <div class="d-flex flex-column font-weight-bold pl-4">
                    <a class="text-white line-height-normal mb-1">{{ $item['user']->name }}</a>
                    <div class="text-white-50 small line-height-normal">
                        {{ $item['total_quiz'] }} quiz
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div class="text-white font-weight-bold h5 mb-0">{{ $item['avg_score'] }}</div>
                <small class="text-white-50">avg</small>
            </div>
        </div>
        @empty
        <div class="text-center text-white-50">
            <i class="fa fa-info-circle"></i> Belum ada data quiz
        </div>
        @endforelse
    </div>
</div>