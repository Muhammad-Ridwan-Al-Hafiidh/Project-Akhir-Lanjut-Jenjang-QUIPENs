<div>
    <div class="card bg-primary">
        <div class="card-header bg-primary text-white border-0 text-center">
            <h5 class="mb-1"><i class="fa fa-trophy me-3"></i> Leaderboard</h5>
        </div>
        <div class="card-body">
            @forelse($users as $user)
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center flex-shrink-0 me-3">
                    <div class="avatar avatar-xl me-3 bg-gray-200">
                        <img class="avatar-img img-fluid" src="{{ URL::to('img/profiles/' . rand(1,12) . '.jpg') }}" alt="">
                    </div>
                    <div class="d-flex flex-column font-weight-bold pl-4">
                        <a class="text-white line-height-normal mb-1">{{ $user->name }}</a>
                        <div class="text-white small line-height-normal">{{ $user->coins }}
                            <i class="fa fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>

    <div class="d-flex w-200 mt-3">
        <div class="mx-auto" style="max-width: 420px; width: 100%;">
            <div class="card">
                <div class="card-body bg-light text-dark">
                    <h5 class="card-title mb-2">Analisa Statistik</h5>
                    <p class="mb-0">Analisa statistik mahasiswa dan nilai rapot</p>
                </div>
            </div>
        </div>
    </div>
</div>
