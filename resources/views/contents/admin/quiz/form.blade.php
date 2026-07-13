@extends('layouts.admin')

@section("content")

<!-- Create Form Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __("Create New Quiz") }}</h6>
                <div class="dropdown no-arrow">
                    <x-BackButton />
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                @if(isset($quiz))
                    <form class="user" method="POST" action="{{ route('quiz.update', $quiz->id) }}">
                        @method('patch')
                @else
                    <form class="user" method="POST" action="{{ route('quiz.store') }}">
                @endif
                    @csrf

                    <!-- Row 1: Title & Attempt -->
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3">
                            <label for="title" class="form-label font-weight-bold">{{ __("Title") }} <span class="text-danger">*</span></label>
                            <input name="title" type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="{{ __('Quiz title') }}" value="{{ $quiz->title ?? '' }}">
                            @error('title')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="attempt" class="form-label font-weight-bold">{{ __("Attempt Limit") }}</label>
                            <input name="attempt" type="number" class="form-control @error('attempt') is-invalid @enderror" id="attempt"
                                min="0" max="50" placeholder="{{ __('0 = unlimited') }}" value="{{ $quiz->attempt ?? '' }}">
                            @error('attempt')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 2: Duration & Min Pass Score -->
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3">
                            <label for="duration" class="form-label font-weight-bold">{{ __("Duration (minutes)") }}</label>
                            <input name="duration" type="number" class="form-control @error('duration') is-invalid @enderror" id="duration"
                                placeholder="{{ __('0 = unlimited') }}" value="{{ $quiz->duration ?? '' }}">
                            @error('duration')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="min_pass_score" class="form-label font-weight-bold">{{ __("Minimum Pass Score (%)") }}</label>
                            <input name="min_pass_score" type="number" class="form-control @error('min_pass_score') is-invalid @enderror" id="min_pass_score"
                                placeholder="{{ __('Default 80') }}" value="{{ $quiz->min_pass_score ?? '80' }}">
                            @error('min_pass_score')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 3: Shuffle & Mentor -->
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3">
                            <label for="is_shuffle" class="form-label font-weight-bold">{{ __("Question Order") }}</label>
                            <select name="is_shuffle" class="form-control @error('is_shuffle') is-invalid @enderror" id="is_shuffle">
                                <option value="1" {{ isset($quiz->is_shuffle) && $quiz->is_shuffle == '1' ? 'selected' : '' }}>{{ __('Shuffle') }}</option>
                                <option value="0" {{ isset($quiz->is_shuffle) && $quiz->is_shuffle == '0' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                            </select>
                            @error('is_shuffle')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="is_mentor" class="form-label font-weight-bold">{{ __("Correction Method") }}</label>
                            <select name="is_mentor" class="form-control @error('is_mentor') is-invalid @enderror" id="is_mentor">
                                <option value="1" {{ isset($quiz->is_mentor) && $quiz->is_mentor == '1' ? 'selected' : '' }}>{{ __('Mentor Correct') }}</option>
                                <option value="0" {{ isset($quiz->is_mentor) && $quiz->is_mentor == '0' ? 'selected' : '' }}>{{ __('System Correct') }}</option>
                            </select>
                            @error('is_mentor')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 4: Show Question & Random Question -->
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3">
                            <label for="show_question" class="form-label font-weight-bold">{{ __("Show Question Type") }}</label>
                            <select name="show_question" class="form-control @error('show_question') is-invalid @enderror" id="show_question">
                                @foreach($show_question as $question)
                                    <option value="{{ $question }}" {{ isset($quiz->show_question) && $quiz->show_question == $question ? 'selected' : '' }}>{{ $question }}</option>
                                @endforeach
                            </select>
                            @error('show_question')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="random_question" class="form-label font-weight-bold">{{ __("Random Question Count") }}</label>
                            <input name="random_question" type="number" class="form-control @error('random_question') is-invalid @enderror" id="random_question"
                                placeholder="{{ __('0 = all questions') }}" value="{{ $quiz->random_question ?? '0' }}">
                            <small class="text-muted d-block mt-1">{{ __('Jika mengisi jumlah per level di bawah, total akan dihitung otomatis dari hard + medium + easy.') }}</small>
                            @error('random_question')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 5: Random by difficulty -->
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3">
                            <label for="easy_questions_count" class="form-label font-weight-bold">{{ __("Easy Questions") }}</label>
                            <input name="easy_questions_count" type="number" min="0" max="100"
                                class="form-control @error('easy_questions_count') is-invalid @enderror" id="easy_questions_count"
                                placeholder="0" value="{{ $quiz->easy_questions_count ?? 0 }}">
                            @error('easy_questions_count')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="medium_questions_count" class="form-label font-weight-bold">{{ __("Medium Questions") }}</label>
                            <input name="medium_questions_count" type="number" min="0" max="100"
                                class="form-control @error('medium_questions_count') is-invalid @enderror" id="medium_questions_count"
                                placeholder="0" value="{{ $quiz->medium_questions_count ?? 0 }}">
                            @error('medium_questions_count')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label for="hard_questions_count" class="form-label font-weight-bold">{{ __("Hard Questions") }}</label>
                            <input name="hard_questions_count" type="number" min="0" max="100"
                                class="form-control @error('hard_questions_count') is-invalid @enderror" id="hard_questions_count"
                                placeholder="0" value="{{ $quiz->hard_questions_count ?? 0 }}">
                            @error('hard_questions_count')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 6: Topics (Full width) -->
                    <div class="form-group">
                        <label for="topics" class="form-label font-weight-bold">{{ __("Select Topics") }}</label>
                        <select name="topics[]" id="topics" multiple style="width: 100%;">
                            @forelse($allTopics ?? [] as $topic)
                                <option value="{{ $topic }}"
                                    @if(isset($quiz) && in_array($topic, $quiz->getTopicsArray())) selected @endif
                                >{{ $topic }}</option>
                            @empty
                                <option disabled>{{ __("No topics available") }}</option>
                            @endforelse
                        </select>
                        @error('topics')
                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Row 7: Description (Full width) -->
                    <div class="form-group">
                        <label for="description" class="form-label font-weight-bold">{{ __("Description") }}</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror editor" id="description"
                            rows="5" placeholder="{{ __('Enter description here...') }}">{{ $quiz->description ?? '' }}</textarea>
                        @error('description')
                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Quiz') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Menyesuaikan Select2 dengan tema SB Admin 2 */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
        min-height: calc(1.5em + 0.75rem + 2px);
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4e73df;
        border-color: #4e73df;
        color: #fff;
        border-radius: 0.2rem;
        padding: 0.2rem 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 0.4rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc;
        background: none;
    }
    /* Memastikan label konsisten */
    .form-label {
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#topics').select2({
            placeholder: "{{ __('Select one or more topics') }}",
            width: "100%",
            allowClear: true,
            closeOnSelect: false,
            language: {
                noResults: function() {
                    return "{{ __('No topics found') }}";
                }
            }
        });
    });
</script>
@endsection
