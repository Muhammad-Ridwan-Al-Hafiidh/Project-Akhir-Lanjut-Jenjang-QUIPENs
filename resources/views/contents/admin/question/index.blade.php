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

             <!-- Filter -->
<form method="GET" class="row g-3 mb-3 align-items-end" id="filter-form">
    <!-- Topic Filter (Dropdown Multiselect) -->
    <div class="col-md-4 col-lg-3">
        <label class="form-label small text-muted mb-1">Topic</label>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm w-100 text-start dropdown-toggle" type="button" id="topicDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @if(request('topic'))
                    {{ count(request('topic')) }} topic(s) selected
                @else
                    All Topics
                @endif
            </button>
            <div class="dropdown-menu p-2" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="topicDropdownBtn">
                <div class="form-check mb-1">
                    <input type="checkbox" class="form-check-input topic-all" id="topic-all" onchange="toggleAll('topic', this)"
                        {{ count((array) request('topic', [])) === count($topics) ? 'checked' : '' }}>
                    <label class="form-check-label" for="topic-all">All Topics</label>
                </div>
                @foreach ($topics as $t)
                    <div class="form-check mb-1">
                        <input type="checkbox" name="topic[]" value="{{ $t }}" class="form-check-input topic-cb" id="topic-{{ $loop->index }}"
                            {{ in_array($t, (array) request('topic', [])) ? 'checked' : '' }}
                            onchange="updateTopicAll()">
                        <label class="form-check-label" for="topic-{{ $loop->index }}">{{ $t }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Difficulty Filter (Toggle Badges) -->
    <div class="col-md-4 col-lg-3">
        <label class="form-label small text-muted mb-1">Difficulty</label>
        <div class="d-flex flex-wrap gap-1">
            @foreach ($difficulties as $d)
                @php
                    $dCls = $d === 'easy' ? 'success' : ($d === 'medium' ? 'warning text-dark' : 'danger');
                    $isChecked = in_array($d, (array) request('difficulty', []));
                @endphp
                <label class="cursor-pointer">
                    <input type="checkbox" name="difficulty[]" value="{{ $d }}" class="d-none"
                        {{ $isChecked ? 'checked' : '' }}
                        onchange="this.closest('form').submit()">
                    <span class="badge bg-{{ $dCls }} {{ $d === 'medium' ? 'text-dark' : 'text-white' }} p-2"
                          style="cursor:pointer; {{ $isChecked ? 'box-shadow: 0 0 0 2px #000;' : 'opacity: 0.7;' }}">
                        {{ ucfirst($d) }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="col-md-auto d-flex gap-2 align-items-center">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filter</button>
        @if(request('topic') || request('difficulty'))
            <a href="{{ route('question.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        @endif
    </div>

    <!-- Active Filter Badges -->
    @php
        $selectedTopics = (array) request('topic', []);
        $selectedDiffs = (array) request('difficulty', []);
    @endphp
    @if($selectedTopics || $selectedDiffs)
    <div class="col-12 d-flex flex-wrap gap-1 mt-2">
        @foreach ($selectedTopics as $st)
            @php
                $remaining = array_values(array_filter($selectedTopics, fn($x) => $x !== $st));
                $topicParam = count($remaining) ? ['topic' => $remaining] : [];
                $url = route('question.index', array_merge(request()->only('difficulty'), $topicParam));
            @endphp
            <span class="badge bg-primary d-inline-flex align-items-center gap-1">
                Topic: {{ $st }}
                <a href="{{ $url }}" class="text-white text-decoration-none"><i class="fas fa-times"></i></a>
            </span>
        @endforeach
        @foreach ($selectedDiffs as $sd)
            @php
                $dCls = $sd === 'easy' ? 'success' : ($sd === 'medium' ? 'warning text-dark' : 'danger');
                $remaining = array_values(array_filter($selectedDiffs, fn($x) => $x !== $sd));
                $diffParam = count($remaining) ? ['difficulty' => $remaining] : [];
                $url = route('question.index', array_merge(request()->only('topic'), $diffParam));
            @endphp
            <span class="badge bg-{{ $dCls }} d-inline-flex align-items-center gap-1 {{ $sd === 'medium' ? 'text-dark' : 'text-white' }}">
                Difficulty: {{ ucfirst($sd) }}
                <a href="{{ $url }}" class="{{ $sd === 'medium' ? 'text-dark' : 'text-white' }} text-decoration-none"><i class="fas fa-times"></i></a>
            </span>
        @endforeach
    </div>
    @endif
</form>

<!-- Scripts -->
<script>
function toggleAll(group, cb) {
    document.querySelectorAll('.' + group + '-cb').forEach(c => c.checked = cb.checked);
}

function updateTopicAll() {
    const cbs = document.querySelectorAll('.topic-cb');
    const all = document.querySelector('.topic-all');
    if (all) all.checked = Array.from(cbs).every(c => c.checked);
}
updateTopicAll();
</script>

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
