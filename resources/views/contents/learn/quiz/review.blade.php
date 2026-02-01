@extends('layouts.admin')


@section("content")

@can('mentor.list')

@include('contents.learn.mentor.mentor-workout')

@endcan

<div class="row">

    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ $activity->title }}</h6>

                <div class="d-flex align-items-center" style="gap:8px;">

                    <div class="dropdown no-arrow">
                        <x-BackButton />
                    </div>
                </div>
            </div>
            <!-- Card Body -->
                        <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card shadow-sm border-bottom-primary">
                            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                                <div class="text-muted">{{ __("Score") }}</div>
                                <div class="h5 m-0">{{ (int)($workout->score ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card shadow-sm border-bottom-info">
                            <div class="card-body py-2">
                                <div class="small text-muted mb-2">Quiz Settings</div>
                                <div class="d-flex flex-wrap" style="gap: 16px;">
                                    <div><strong>Random Questions:</strong> {{ (int)($activity->random_question ?? 0) }}</div>
                                    <div><strong>Shuffle:</strong> {{ (int)($activity->is_shuffle ?? 0) === 1 ? 'Yes' : 'No' }}</div>
                                    <div><strong>Selected (this workout):</strong> {{ (int)($activity->questions->count() ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php($weights = ['easy' => 1, 'medium' => 2, 'hard' => 3])
                <div class="row mb-3">

                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card shadow-sm border-bottom-success">
                            <div class="card-body py-2">
                                <div class="small text-muted mb-2">Difficulty Mean & Conclusion</div>
                                <div class="d-flex flex-wrap" style="gap: 16px;">
                                    <div><strong>Legend:</strong> easy=1, medium=2, hard=3</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>@livewire('activity.result', [
                'activity' => $activity,
                'participant' => $participant,
                'workout' => $workout
                ])
            </div>

            <div class="card-footer text-center">
                <form method="post" class="d-inline" action="{{ route('quizRestart', $workout->id) }}" onsubmit="return confirm('Restart this quiz? This will clear previous answers.')">
                    @csrf
                    <button class="btn btn-warning" id="restartQuiz">
                        <i class="fa fa-redo"></i> {{ __("Restart") }}
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div class="d-none">
        <div id="dialog-confirm" title="Save And Close?">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
                These items will be permanently close and cannot be recovered. Are you sure?</p>
        </div>
    </div>
    @endsection
