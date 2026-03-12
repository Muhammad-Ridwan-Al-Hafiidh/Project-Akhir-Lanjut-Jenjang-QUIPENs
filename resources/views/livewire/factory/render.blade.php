<div class="row">
    {{-- Question Type --}}
    <div class="col-md-4 mb-3">
        <label for="questionTypeId" class="form-label fw-bold">{{ __('Question Type') }}</label>
        <select id="questionTypeId" wire:model="questionTypeId" wire:change="selectQuestionType" class="form-select">
            @forelse ($questionTypes as $types)
                <option value="{{ $types->id }}">{{ str_replace('Question' , '' , $types->title) }}</option>
            @empty
                <option value="">No question types</option>
            @endforelse
        </select>
    </div>

    {{-- Quiz Selected (if any) --}}
    @if($quiz)
    <div class="col-md-4 mb-3">
        <label for="quiz_selected" class="form-label fw-bold">{{ __('Quiz') }}</label>
        <input type="text" id="quiz_selected" class="form-control" value="{{ $quiz->title ?? '' }}" disabled>
    </div>
    @endif

    {{-- Question Form Component --}}
    @if($component)
        <div class="col-12">
            @livewire($component, [
                'questionTypeId' => $questionTypeId,
                'question' => $question,
                'quiz' => $quiz
            ], key($component . '-' . $questionTypeId))
        </div>
    @endif
</div>