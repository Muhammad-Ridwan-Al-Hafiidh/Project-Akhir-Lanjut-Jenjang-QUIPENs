<div>
    <div class="form-group row">
        <div class="col-sm-12 mb-3 mb-sm-0">
            <input type="text" 
                class="form-control form-control-user" 
                wire:model="search"
                placeholder="Search">
        </div>
    </div>
    
    <hr/>

    @forelse ($questions as $question)
        @php
            $diffColor = match($question->difficulty) {
                'easy' => 'success',
                'medium' => 'warning',
                'hard' => 'danger',
                default => 'secondary'
            };
        @endphp
        
        <div class="card border-left-success bg-light shadow mb-2">
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
                        <a href="{{ route($route, ['parent' => $parent, 'question' => $question->id]) }}"
                           class="btn btn-circle btn-sm btn-success">
                            <i class="fas fa-plus text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-3">
            <i class="fas fa-search fa-2x mb-2"></i>
            <p class="mb-0">No questions found.</p>
        </div>
    @endforelse

    <div class="mt-3">
        {{ $questions->links() }}
    </div>
</div>