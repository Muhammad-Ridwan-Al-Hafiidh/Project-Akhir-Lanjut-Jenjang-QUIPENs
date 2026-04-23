# TOPIC-BASED QUESTION SELECTION SYSTEM - COMPLETE IMPLEMENTATION SUMMARY

## 1. FORM LAYER (Quiz Creation/Edit)
### Updated: resources/views/contents/admin/quiz/form.blade.php
- ? Added topics multi-select field (Select2)
- ? Shows all available topics from questions table
- ? Pre-selects topics when editing existing quiz
- ? Includes Select2 CSS/JS for rich UI

### Key Features:
```blade
<select name="topics[]" id="topics" class="form-control" multiple>
    @forelse($allTopics as $topic)
        <option value="{{ $topic }}"
            @if(isset($quiz))
                {{ in_array($topic, $quiz->getTopicsArray()) ? 'selected' : '' }}
            @endif
        >{{ $topic }}</option>
    @endforelse
</select>
```

---

## 2. BACKEND - QUIZ CONTROLLER
### File: app/Http/Controllers/QuizController.php

#### Create Method:
1. Gets all unique topics from questions: `$allTopics`
2. Passes to form view
3. On store: Extracts topics from POST and saves to `quiz_topics` table

#### Update Method:
1. Deletes old topics for quiz
2. Re-inserts selected topics

#### Key Code:
```php
public function create()
{
    $allTopics = \App\Models\Question::distinct()->pluck('topic')->toArray();
    $show_question = $this->show_question;
    return view('contents.admin.quiz.form', compact('show_question', 'allTopics'));
}

public function store(QuizRequest $request)
{
    $topics = $request->input('topics', []);
    $data = $request->except('topics');
    $quiz = Quiz::create($data);
    
    if (!empty($topics)) {
        foreach ($topics as $topic) {
            DB::table('quiz_topics')->insert([
                'quiz_id' => $quiz->id,
                'topic' => $topic,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
```

---

## 3. DATABASE MODELS
### Quiz.php (app/Models/Quiz.php)

```php
// Relationship to topics
public function topics(): HasMany
{
    return $this->hasMany(QuizTopic::class);
}

// Get array of topic names
public function getTopicsArray()
{
    return $this->topics()->pluck('topic')->toArray();
}

// Get questions filtered by selected topics
public function getQuestionsFilteredByTopics()
{
    $topics = $this->getTopicsArray();
    return Question::when(!empty($topics), function($q) use ($topics) {
        return $q->whereIn('topic', $topics);
    })->get();
}
```

### Question.php
- ? Has `topic` column (added via migration 2026_03_12_065113)
- ? Stores values like "Jaringan Komputer", "Struktur Data", etc.

---

## 4. QUESTION SELECTION LOGIC
### File: app/Utility/Workout/WorkoutService.php
### Method: setWorkOutQuizSyncForThisExcersice()

### CRITICAL UPDATES:

#### Random Mode (random_question > 0):
```php
$n = (int) ($quiz->random_question ?? 0);
$topicsArray = $quiz->getTopicsArray();
$hasTopics = !empty($topicsArray);

if ($n > 0) {
    $query = Question::query();
    
    // ? FILTER BY TOPICS
    if ($hasTopics) {
        $query->whereIn('topic', $topicsArray);
    }
    
    // ? FILTER BY DIFFICULTY
    if ($difficulty) {
        $query->where('difficulty', $difficulty);
    }
    
    $questions = $query->inRandomOrder()->limit($n)->get();
}
```

#### Attached Mode (random_question = 0):
```php
else {
    $questions = $quiz->Questions;
    
    // ? FILTER ATTACHED BY TOPIC
    if ($hasTopics) {
        $questions = $questions->filter(function($q) use ($topicsArray) {
            return in_array($q->topic, $topicsArray);
        })->values();
    }
    
    // ? FALLBACK: If no questions attached, use question bank
    if ($questions->count() === 0 && $hasTopics) {
        $questions = Question::whereIn('topic', $topicsArray)->get();
    }
    
    // ? APPLY DIFFICULTY FILTER
    if ($difficulty) {
        $filtered = collect($questions)->filter(function ($q) use ($difficulty) {
            return isset($q->difficulty) ? 
                ($q->difficulty == $difficulty) : false;
        })->values();

        if ($filtered->count() > 0) {
            $questions = $filtered;
        }
    }
    
    // ? APPLY SHUFFLE/SORT
    if ((int)($quiz->is_shuffle ?? 0) === 1) {
        $questions = $questions->shuffle()->values();
    } else {
        $questions = $questions->sortBy(function($q) {
            return optional($q->pivot)->order ?? 0;
        })->values();
    }
}
```

---

## 5. WORKOUT LOGS
### File: WorkoutQuizLog
- Stores: `workout_id`, `quiz_id`, `question_id`
- These are the selected questions that will be shown to student

### Verification Query:
```sql
SELECT wql.id, wql.workout_id, wql.quiz_id, wql.question_id, q.topic
FROM workout_quiz_logs wql
LEFT JOIN questions q ON wql.question_id = q.id
ORDER BY wql.created_at DESC
```

---

## 6. DDA INTEGRATION (FastAPI Service)
### File: c:\KULIAH D4 LANJUT JENJANG\PA\dda_service\app\main.py

#### Endpoint: POST /recommend
```python
@app.post("/recommend", response_model=DDAResponse)
async def get_recommendation(dda_input: DDAInput):
    # Learn from previous answer with topic levels
    agent.update_q_table(dda_input)
    
    # Choose next difficulty based on topics
    available_topics = list(set(
        log.topic for log in dda_input.session_logs
    )) if dda_input.session_logs else ["general"]
    
    next_topic = random.choice(available_topics)
    next_difficulty = agent.choose_action(dda_input, next_topic)
    
    _, topic_level = agent.get_topic_recommendation(dda_input, next_topic)
    
    return DDAResponse(
        next_difficulty=next_difficulty,
        next_topic=next_topic,
        recommended_topic_level=topic_level
    )
```

#### Models: app/models.py
```python
class ExamLog(BaseModel):
    question_id: int
    difficulty: str  # 'easy', 'medium', 'hard'
    is_correct: bool
    topic: str  # ? TOPIC FIELD
    answer_time_seconds: Optional[float] = None

class DDAInput(BaseModel):
    user_id: str
    session_logs: List[ExamLog]
    topic_levels: Optional[Dict[str, int]] = None  # ? TRACK PER TOPIC

class DDAResponse(BaseModel):
    next_difficulty: str
    next_topic: str
    recommended_topic_level: Optional[int] = None
```

#### RL Agent: app/rl_agent.py
```python
def choose_action(self, dda_input: DDAInput, topic: str = None) -> str:
    """Choose difficulty based on accuracy & topic level"""
    accuracy_state, topic_level_state = self._get_combined_state(
        dda_input, topic
    )
    
    # Q-learning: state = (accuracy, topic_level)
    action_index = int(np.argmax(
        self.q_table[accuracy_state, topic_level_state, :]
    ))
    
    return self.actions[action_index]
```

---

## 7. COMPLETE FLOW DIAGRAM

```
USER CREATES QUIZ
    ?
[Quiz Form] - Select Topics (e.g., "Jaringan Komputer", "Struktur Data")
    ?
[QuizController.store()] - Save quiz_topics
    ?
[Quiz Model] - topics() relationship loaded
    ?
[Workout Created] - User starts quiz
    ?
[WorkoutController.restart()] - Call setWorkOutQuizSyncForThisExcersice()
    ?
[WorkoutService] - Get questions for this workout
    +- Mode 1: Random (random_question > 0)
    ¦   +- Get $quiz->getTopicsArray() 
    ¦   +- Query: Question::whereIn('topic', $topics)
    ¦   +- Filter by difficulty (if DDA)
    ¦   +- Take N random ? WorkoutQuizLog
    ¦
    +- Mode 2: Attached (random_question = 0)
        +- Get $quiz->Questions (attached)
        +- Filter: only with matching topic
        +- Fallback: if empty, use question bank
        +- Filter by difficulty (if DDA)
        +- Shuffle/sort
        +- Save ? WorkoutQuizLog
    ?
[Livewire Component] - Load questions from WorkoutQuizLog
    ?
[Frontend] - Display questions (all now match selected topics!)
    ?
[Student Answers Questions]
    ?
[DDA Analysis] - Analyze with topics for next difficulty
```

---

## 8. CURRENT STATE VERIFICATION

### ? Database Schema
- `questions` table has `topic` column (not nullable, indexed)
- `quiz_topics` table (quiz_id, topic)
- `question_quiz` pivot table (quiz_id, question_id, order)

### ? Sample Data
- Questions: 100 total
  - Jaringan Komputer: 17
  - Database: 17
  - Web Pemrograman: 16
  - Pemrograman: 16
  - Struktur Data: 13
  - Dasar Komputer: 12
  - Sistem Operasi: 9

### ? Quiz Topics Example
- Quiz 1: Sistem Operasi
- Quiz 4: Jaringan Komputer, Struktur Data, Web Pemrograman
- Quiz 5: Database

### ? Workout Quiz Logs
- Only questions matching quiz topics are selected
- DDA difficulty respected
- First attempt forced to non-DDA

---

## 9. TESTING THE SYSTEM

### Step 1: Create Quiz with Topics
1. Go to Quiz Create form
2. Select 2-3 topics (e.g., "Jaringan Komputer", "Struktur Data")
3. Set random_question to 10
4. Save

### Step 2: Start Workout
1. Click on quiz
2. System calls: setWorkOutQuizSyncForThisExcersice()
3. WorkoutQuizLog entries created with topic-filtered questions

### Step 3: Verify
```sql
-- Check if questions match selected topics
SELECT q.topic, COUNT(*) as cnt
FROM workout_quiz_logs wql
JOIN questions q ON wql.question_id = q.id
WHERE wql.workout_id = <WORKOUT_ID>
GROUP BY q.topic;
-- Should show ONLY selected topics!
```

---

## 10. KEY IMPROVEMENTS MADE

1. ? **Form Update**: Added multi-select topics to quiz creation
2. ? **Database Relationships**: Proper HasMany relationship in Quiz model
3. ? **Question Filtering**: Both random & attached modes filter by topic
4. ? **Fallback Logic**: If no attached questions, search from question bank
5. ? **DDA Integration**: RL agent receives topic info for per-topic difficulty
6. ? **Data Models**: Pydantic models support topic_levels tracking
7. ? **Analysis**: FastAPI service calculates topic distribution

---

## 11. STILL TODO (Optional Enhancements)

- [ ] Add topic difficulty mapping (e.g., "Jaringan Komputer" is harder)
- [ ] Implement topic prerequisites (must learn A before B)
- [ ] Export reports grouped by topic
- [ ] Topic mastery indicators
- [ ] Recommend topics to focus on

