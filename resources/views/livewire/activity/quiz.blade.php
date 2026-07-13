<div id="btnQuestions" class="d-flex flex-wrap justify-content-center gap-1">
        
    @forelse ($questions as $question)
        <button id="btnQuestion-{{ $question->id }}" onclick="showQuestion('{{ $question->id }}')" class="process-step btnQuestion" style="min-width: 32px; padding: 4px 8px; font-size: 0.8rem;">
            <span class="process-label">{{ $loop->iteration }}</span>
        </button>
    @empty  
    <div class="text-muted small">No questions available.</div>
    @endforelse

</div>
<hr/>
<div id="questions" style="display: none" class="row">
    <div class="col-12 p-4 text-center">
    {!! $questionsRender !!}
    </div>
</div>



<script>
    var style = "{{ $style }}";
</script>