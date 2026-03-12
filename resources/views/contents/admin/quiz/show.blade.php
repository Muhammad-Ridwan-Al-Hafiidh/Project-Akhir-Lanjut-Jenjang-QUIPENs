@extends('layouts.admin')


@section("content")
<div class="row">
    
    <div class="col-6">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ $quiz->title }}</h6>
                <div class="dropdown no-arrow">
                    <x-BackButton />
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <p>{!! $quiz->description !!}</p>
                
                <hr/>

                @forelse ($quiz->Questions as $question)
                    @php
                        $diffColor = match($question->difficulty) {
                            'easy' => 'success',
                            'medium' => 'warning',
                            'hard' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    
                    <div class="card border-left-primary bg-light shadow mb-2">
                        <div class="card-body p-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="text-xs font-weight-bold text-uppercase me-2">
                                            {{ $question->title }}
                                        </span>
                                        <span class="badge bg-{{ $diffColor }} text-white me-1" style="font-size: 10px;">
                                            {{ ucfirst($question->difficulty ?? 'N/A') }}
                                        </span>
                                        @if($question->topic)
                                        <span class="badge bg-primary text-white" style="font-size: 10px;">
                                            {{ $question->topic }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if(!$loop->first)
                                    <a href="{{ route('orderChangeQuestion' , ['from' => $question->pivot->id , 'move' => 'up' ]) }}" 
                                       class="btn btn-circle btn-sm btn-secondary">
                                        <i class="fas fa-sort-up text-dark-300"></i>
                                    </a>
                                    @endif

                                    @if(!$loop->last)
                                    <a href="{{ route('orderChangeQuestion' , ['from' => $question->pivot->id , 'move' => 'down' ]) }}"
                                       class="btn btn-circle btn-sm btn-secondary">
                                        <i class="fas fa-sort-down text-dark-300"></i>
                                    </a>
                                    @endif

                                    <a href="{{ route('deleteQuestionAsQuiz' ,['quizQuestion' => $question->pivot->id ]) }}" 
                                       class="btn btn-circle btn-sm btn-danger">
                                        <i class="fas fa-times text-dark-300"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">No questions added yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card shadow mb-4 border-bottom-success">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __("Questions") }}</h6>
                <div class="dropdown no-arrow">
                    <x-CreateButton path="{{ route('question.create'). '?quiz_id=' . $quiz->id }}" />
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="text-center">
                    @livewire('container.show-questions', [
                        'route' => 'addQuestionToQuiz',
                        'parent' => $quiz->id
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection