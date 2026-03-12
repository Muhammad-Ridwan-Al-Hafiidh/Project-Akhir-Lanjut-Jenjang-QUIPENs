<div class="card shadow-sm">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="m-0 fw-bold">
            <i class="fas fa-list-ol me-2"></i> Multiple Choice Question
        </h6>
    </div>
    <div class="card-body">
        <input type="hidden" wire:model="questionTypeId" />
        
        {{-- Difficulty & Topic Row --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="difficulty" class="form-label fw-bold">
                    <i class="fas fa-signal me-1 text-muted"></i> Difficulty
                </label>
                <select class="form-select" id="difficulty" wire:model="difficulty">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="topic" class="form-label fw-bold">
                    <i class="fas fa-tag me-1 text-muted"></i> Topic
                </label>
                <input type="text" class="form-control" id="topic" wire:model="topic" placeholder="e.g. Array, Loop, OOP">
            </div>
        </div>

        {{-- Title --}}
        <div class="mb-3">
            <label for="titleofquestion" class="form-label fw-bold">
                <i class="fas fa-heading me-1 text-muted"></i> Question Title
            </label>
            <input type="text" class="form-control" id="titleofquestion" wire:model="title" placeholder="Enter question title">
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="descriptionofquestion" class="form-label fw-bold">
                <i class="fas fa-align-left me-1 text-muted"></i> Question Body
            </label>
            <textarea class="form-control" id="descriptionofquestion" wire:model="question_body" rows="4" placeholder="Enter the question description or content"></textarea>
        </div>

        {{-- Answers Section --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-check-circle me-1 text-muted"></i> Answer Options
            </label>
            <p class="text-muted small mb-2">Check the box to mark the correct answer(s)</p>
            
            @forelse($answers as $index => $answer)
            <div class="input-group mb-2">
                <div class="input-group-text bg-light">
                    <input type="checkbox" name="correctAnswer[]" value="answer-{{ $index }}" 
                           wire:model="correctAnswer.{{ $index }}" 
                           class="form-check-input mt-0" 
                           title="Mark as correct answer">
                </div>
                <span class="input-group-text bg-secondary text-white">{{ $loop->iteration }}</span>
                <input type="text" class="form-control" wire:model="answers.{{ $index }}" placeholder="Enter answer option">
                <button class="btn btn-outline-danger" wire:click.prevent="removeAnswer('{{ $index }}')" title="Remove answer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            @empty 
            <div class="alert alert-warning py-2">
                <i class="fas fa-exclamation-triangle me-1"></i> No answer options added yet. Click "Add Answer" below.
            </div>
            @endforelse
            
            <button wire:click.prevent="addNewAnswer" class="btn btn-success btn-sm mt-2">
                <i class="fas fa-plus me-1"></i> Add Answer
            </button>
        </div>
    </div>
    <div class="card-footer bg-light">
        <button wire:click.prevent="store" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save Question
        </button>
    </div>
</div>

{{-- Preview --}}
@include('livewire.factory.question.multiple-question.review')