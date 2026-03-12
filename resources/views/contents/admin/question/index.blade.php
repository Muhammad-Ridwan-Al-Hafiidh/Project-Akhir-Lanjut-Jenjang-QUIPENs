@extends('layouts.admin')


@section("content")
<div class="row">

    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-question-circle me-2"></i> {{ __("Question Bank") }}
                </h6>
                <div class="dropdown no-arrow">
                    @can('question.create')
                    <x-CreateButton path="{{ route('question.create') }}" />
                    @endcan
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">{{ __("Title") }}</th>
                                <th scope="col" style="width: 130px;">{{ __("Type") }}</th>
                                <th scope="col" style="width: 100px;">{{ __("Difficulty") }}</th>
                                <th scope="col" style="width: 130px;">{{ __("Topic") }}</th>
                                @if(Auth::user()->hasRole('Super-Admin') || Auth::user()->hasAnyPermission(['question.edit' , 'question.delete']))
                                <th scope="col" style="width: 120px;">{{ __("Action") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($questions as $question)
                                @php
                                    $diffColor = match($question->difficulty) {
                                        'easy' => 'success',
                                        'medium' => 'warning',
                                        'hard' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $question->title }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white">{{ $question->QuestionType->title }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $diffColor }} text-white">{{ ucfirst($question->difficulty ?? 'N/A') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($question->topic)
                                            <span class="badge bg-primary text-white">{{ $question->topic }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->hasRole('Super-Admin') || Auth::user()->hasAnyPermission(['question.edit' , 'question.delete']))
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('question.edit')
                                                <x-EditButton itemId="{{ $question->id }}" path="question.edit" />
                                            @endcan
                                            @can('question.delete')
                                                <x-DeleteButton itemId="{{ $question->id }}" path="question.destroy" />
                                            @endcan
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-0 fw-semibold">No questions available.</p>
                                        <small>Click "New" to create your first question.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($questions->hasPages())
                <hr/>
                <div class="d-flex justify-content-center">
                    {!! $questions->links() !!}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection