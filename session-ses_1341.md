# New session - 2026-06-15T15:22:11.293Z

**Session ID:** ses_1341fc222ffexQUjGcGABzzU1w
**Created:** 6/15/2026, 10:22:11 PM
**Updated:** 6/15/2026, 10:25:32 PM

---

## User

anlisis code saya dan juga dda service nya di  C:\KULIAH D4 LANJUT JENJANG\PA\dda_service> jadi projek saya adalah aplikasi Desain Sistem Ujian Online dengan Dynamic Difficulty Adjustment (DDA) Berbasis Reinforcement Learning Penelitian ini bertujuan merancang sistem ujian online yang dapat menyesuaikan tingkat kesulitan soal secara dinamis berdasarkan kemampuan peserta. Dengan menggunakan metode Reinforcement Learning (RL), sistem akan mempelajari pola jawaban peserta dan memilih soal berikutnya yang sesuai dengan tingkat kemampuan mereka. Tujuan akhirnya adalah menciptakan pengalaman ujian yang lebih personalized, sekaligus meningkatkan akurasi dalam mengukur kompetensi peserta. 

---

## Assistant (Build · DeepSeek V4 Flash Free · 183.6s)

_Thinking:_

The user wants me to analyze their code project which consists of two parts:
1. `C:\KULIAH D4 LANJUT JENJANG\PA\laramint` - the main Laravel application (current workspace)
2. `C:\KULIAH D4 LANJUT JENJANG\PA\dda_service` - the DDA service

They want me to analyze the code and understand how the project works - an Online Exam System with Dynamic Difficulty Adjustment based on Reinforcement Learning.

Let me start by exploring both directories to understand the project structure.

**Tool: task**

**Input:**
```json
{
  "description": "Explore laramint project",
  "prompt": "Explore the project at C:\\KULIAH D4 LANJUT JENJANG\\PA\\laramint thoroughly. I need to understand:\n\n1. The overall project structure (directories, key files)\n2. What framework/technologies are used\n3. The database schema (migrations)\n4. Models and their relationships\n5. Routes (API and web)\n6. Controllers and their logic\n7. Any files related to DDA (Dynamic Difficulty Adjustment), Reinforcement Learning, exams, questions, answers, etc.\n8. Frontend components related to exams\n9. Any configuration for AI/ML services\n\nPlease read the main files: composer.json, routes/api.php, routes/web.php, key controllers, key models, any JavaScript/Vue components related to exams, and any DDA-related files.\n\nReturn a comprehensive analysis of the project structure, architecture, and how the exam system works.",
  "subagent_type": "explore"
}
```

**Output:**
```
<task id="ses_1341faac6ffeaxfsyBg7IE8n9P" state="completed">
<task_result>
I now have a comprehensive understanding of the project. Here is the full analysis:

---

## COMPREHENSIVE PROJECT ANALYSIS: LARAMINT

### 1. OVERALL PROJECT STRUCTURE

```
laramint/
├── app/
│   ├── Actions/Fortify/          # Laravel Fortify authentication actions
│   ├── Console/                  # Artisan commands
│   ├── Events/                   # Event classes
│   ├── Exceptions/               # Exception handler
│   ├── Http/
│   │   ├── Controllers/          # Web controllers (grouped by domain)
│   │   │   ├── Acl/              # Role/Permission/User management
│   │   │   ├── Admin/            # Admin-only (Badges, Menu)
│   │   │   ├── Analytics/        # DDA Metrics, DDA Comparison, SPK, Topic Comparison
│   │   │   ├── Auth/             # Authentication
│   │   │   ├── Education/        # Courses, Departments, Terms
│   │   │   ├── Front/            # Public-facing pages
│   │   │   ├── learn/            # Workout/Quiz execution
│   │   │   ├── Mentors/          # Mentor dashboard
│   │   │   └── Panel/            # MyCourse panel
│   │   ├── Livewire/             # Livewire components (Activity, Admin, Box, Container, Factory, Forms, Services, User)
│   │   ├── Middleware/            # Custom middleware
│   │   └── Requests/             # Form request validation
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mailables
│   ├── Models/                   # 30 Eloquent models
│   ├── Notifications/            # Notification classes
│   ├── Observers/                # Model observers
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Repository pattern (Base, Contracts, Models)
│   ├── Services/                 # Service layer (Back, Front, Traits, Units)
│   ├── Traits/                   # Shared traits (e.g., Sequence)
│   ├── Utility/                  # Core domain logic
│   │   ├── Modules/Tasks/        # Task adapters (Quiz, Document, File, Feedback, Rubric)
│   │   ├── Question/             # Question type adapters (8 types)
│   │   └── Workout/              # WorkoutService (quiz engine)
│   └── View/                     # View composers
├── bootstrap/
├── config/                       # 20 config files
├── database/
│   ├── factories/
│   ├── migrations/               # 43 migration files
│   └── seeders/                  # 16 seeders
├── fastapi-dda/                  # Python FastAPI DDA microservice (virtual env only, no source .py files yet)
│   ├── .venv/                    # Python 3.12 virtual env with FastAPI, uvicorn, sqlmodel, pydantic, etc.
│   └── src/                      # Empty source directory (only __pycache__)
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js
│   │   ├── dda-topic-levels.js   # Frontend JS for DDA topic level visualization
│   │   └── owl-carousel.js
│   ├── lang/
│   └── views/
│       ├── contents/
│       │   ├── admin/analytics/  # DDA Metrics, DDA Comparison, SPK, Topic Comparison
│       │   ├── learn/quiz/       # Quiz show, review, prepare, DDA card, topic levels card
│       │   └── ...
│       └── livewire/             # Livewire component templates
├── routes/
│   ├── api.php                   # Single API route (/api/user)
│   ├── web.php                   # All web routes (front, learn, mentor, panel/admin)
│   ├── channels.php
│   └── console.php
└── storage/
```

---

### 2. FRAMEWORK & TECHNOLOGIES

| Technology | Version/Purpose |
|---|---|
| **Laravel Framework** | 9.x (PHP 7.3\|8.0+) |
| **Livewire** | 2.8 - Full-stack framework for dynamic UI (quiz rendering, activity results) |
| **Laravel Fortify** | 1.8 - Authentication backend (login, 2FA, email verification) |
| **Laravel Sanctum** | 2.11 - API token authentication |
| **Spatie Laravel Permission** | 5.4 - Role-based access control (Super-Admin, Admin, Mentor, Student roles) |
| **Spatie Laravel Activitylog** | 4.4 - Model activity logging |
| **Laravel Telescope** | 4.9 (dev) - Debugging/monitoring |
| **Laravel Debugbar** | 3.6 (dev) - Debug toolbar |
| **Laravel Sail** | 1.x (dev) - Docker dev environment |
| **Doctrine DBAL** | 3.2 - Schema introspection for migrations |
| **Awobaz Compoships** | 2.1 - Composite key relationships |
| **Laravel Mix** | 6.x - Asset compilation |
| **Chart.js** | 3.x (CDN) - Analytics charts (DDA metrics, comparison) |
| **FastAPI (Python)** | 0.128 (in `fastapi-dda/.venv`) - Planned DDA recommendation microservice |
| **SQLModel/SQLAlchemy** | In venv - For potential FastAPI DB access |
| **Database** | MySQL (database: `mint`) |

---

### 3. DATABASE SCHEMA (Migrations - 43 files)

**Core Entities:**

| Table | Key Columns | Purpose |
|---|---|---|
| `users` | id, name, email, password, 2FA columns | User accounts |
| `departments` | id, title | Academic departments |
| `courses` | id, title, department_id | Courses belonging to departments |
| `terms` | id, title, course_id, is_published | Semesters/terms for a course |
| `sessions` | id, title | Learning sessions within a term |
| `sessionables` | id, session_id, sessionable_type, sessionable_id, order, title, type | Polymorphic pivot: attaches quizzes/files/documents/rubrics/feedbacks to sessions |
| `term_user` (participants) | id, term_id, user_id, role_id | Pivot: users enrolled in terms with roles |
| `term_session` | id, term_id, session_id, order | Pivot: sessions ordered within terms |

**Quiz/Question Entities:**

| Table | Key Columns | Purpose |
|---|---|---|
| `quizzes` | id, title, description, attempt, duration, is_mentor, is_shuffle, min_pass_score, show_question (StepByStep\|OnePage), random_question, soft_deletes | Quiz configuration |
| `question_types` | id, title | Question type definitions (e.g., MultipleChoice, Essay, TrueFalse, etc.) |
| `questions` | id, title, question_body, question_type_id, answer (json), **difficulty** (easy/medium/hard), **topic** (string), soft_deletes | Question bank with difficulty and topic |
| `question_quiz` | id, quiz_id, question_id, order, score | Pivot: questions assigned to quizzes with ordering |
| `quiz_topics` | id, quiz_id, topic (string), unique(quiz_id, topic) | Topics assigned to quizzes for filtering |

**Workout (Exercise/Attempt) Entities:**

| Table | Key Columns | Purpose |
|---|---|---|
| `workouts` | id, participant_id, sessionable_id, date_first_view, date_last_view, is_completed, is_mentor, score, date_get_score, **used_dda** (bool), **current_dda_difficulty** (nullable) | Records a student's attempt at an activity |
| `workout_quiz_logs` | id, workout_id, quiz_id, question_id, answer (json), is_mentor, score | Individual question answers during a workout |
| `workout_restart_logs` | id, workout_id, user_id, previous_score, **dda_difficulty**, **non_dda_difficulty**, **topic_levels** (json), **used_dda** (bool), payload (json) | History of restart attempts with DDA metadata |

**Other Entities:**
`files`, `documents`, `document_file`, `rubrics`, `feedback`, `feedback_questions`, `plans`, `badges`, `coins_logs`, `mentor_comments`, `configurations`, `notifications`, `activity_log`, `personal_access_tokens`, `jobs`, `failed_jobs`, `password_resets`

---

### 4. MODELS & RELATIONSHIPS

**User** (extends Authenticatable)
- Has Roles (Spatie), HasApiTokens (Sanctum)
- `Terms()` - BelongsToMany via `term_user`
- `Participants()` - HasMany via `term_user`
- `CoinsLogs()` - HasMany

**Term**
- `Department()` - BelongsTo
- `Course()` - BelongsTo
- `Participants()` - BelongsToMany(User) with pivot `role_id`
- `Sessions()` - BelongsToMany(Session) with pivot `order`
- `WorkoutByUser(User)` - HasMany(Workout) scoped by user
- `getAllActivitiesAttribute` - Aggregates all sessionables across sessions

**Session**
- Polymorphic many-to-many via `sessionables`: `documents()`, `Files()`, `Quizes()`, `Feedbacks()`, `Rubrics()`
- `related()` - HasMany(Sessionable) ordered
- `Terms()` - BelongsToMany(Term)
- `Workout()` - HasMany

**Sessionable** (polymorphic pivot)
- `Model()` - BelongsTo the morphed model (Quiz/File/Document etc.)

**Quiz**
- `Questions()` - BelongsToMany(Question) with pivot `order`, `score`
- `Workout(term_id, session_id, sessionable_id)` - HasOne scoped workout
- `topics()` - HasMany(QuizTopic)
- `getQuestionsFilteredByTopics()` - Filters questions by assigned topics

**Question**
- `QuestionType()` - BelongsTo
- Fields: difficulty (easy/medium/hard), topic (string)
- Activity logging enabled

**QuizQuestion** (pivot model for `question_quiz`)
- Simple model with guarded fillable

**Workout**
- `Sessionable()` - BelongsTo
- `Session()` - BelongsTo
- `WorkOutQuiz()` - HasMany(WorkoutQuizLog)
- `User()` - BelongsTo
- `Participant()` - HasOne
- `RestartLogs()` - HasMany(WorkoutRestartLog)
- Uses Compoships for composite key relationships

**WorkoutQuizLog**
- `Question()` - BelongsTo
- Stores per-question answer (json), score

**WorkoutRestartLog**
- `workout()` - BelongsTo
- Casts: `payload` as array, `topic_levels` as json
- Stores DDA-related metadata per restart attempt

**Participant** (pivot model for `term_user`)
- `User()` - HasOne
- `Term()` - HasOne
- `Workout()` - HasMany
- `scopeLearners()` - Filters by mentor's terms

**QuizTopic**
- `quiz()` - BelongsTo

---

### 5. ROUTES

#### API Routes (`routes/api.php`)
- `GET /api/user` - Authenticated user info (sanctum)

#### Web Routes (`routes/web.php`)

**Public (Front):**
- `GET /` - Home page
- `GET /front/courses`, `/front/course/{id}`, `/front/plans`

**Learner (authenticated, verified):**
- `GET /learn/task/{participant}/{sessionable}` - View task (quiz/workout)
- `POST /learn/task/{participant}/{sessionable}` - Start prepared task
- `POST /learn/quiz/workout` - Submit quiz answer (AJAX)
- `GET /learn/my/course` - My courses list
- `GET /learn/my/course/{participant}` - Enter course learning
- `GET /learn/completeAndNext/{workout}` - Complete current + go next
- `POST /learn/quiz/restart/{workout}` - Restart quiz with optional DDA

**Mentor (authenticated, verified):**
- `GET /mentor/learners` - My learners list
- `GET /mentor/learner/{user}` - Show learner terms
- `GET /mentor/workout/{participant}` - Participant workouts
- `GET /mentor/review/workout/{participant}/{workout}` - Review workout
- `POST /mentor/review/update` - Update mentor review
- **Analytics routes:**
  - `GET /mentor/topic-comparison` - Topic level comparison page
  - `POST /mentor/topic-comparison/data` - Get comparison data
  - `GET /mentor/topic-comparison/students` - Get students for term
  - `GET /mentor/topic-comparison/quizzes` - Get available quizzes
  - `POST /mentor/topic-comparison/quiz` - Per-quiz comparison
  - `POST /mentor/topic-comparison/dda-metrics` - DDA metrics
  - `POST /mentor/topic-comparison/spk-recommendations` - SPK recommendations

**Panel/Admin (authenticated, verified):**
- `GET /panel/dashboard`, `/panel/settings`, `/panel/profile`
- **Resourceful CRUD:** department, course, term, session, file, activity, document, quiz, question, rubric, feedback, plan, badges
- **Quiz-Question management:** ordering, add/delete questions
- **Session management:** add document/file/quiz/rubric/feedback to session, ordering
- **ACL (Super-Admin only):** user, role, permission management
- **Analytics routes:**
  - `GET /panel/analytics/topic-level-comparison`
  - `GET /panel/analytics/course-students` - Shared student list
  - `GET /panel/analytics/dda-metrics` - DDA Metrics page
  - `POST /panel/analytics/dda-metrics/data` - Get DDA metrics data
  - `GET /panel/analytics/dda-comparison` - DDA vs Non-DDA comparison
  - `POST /panel/analytics/dda-comparison/data` - Get comparison data
  - `GET /panel/analytics/spk-recommendations` - SPK recommendations page
  - `POST /panel/analytics/spk-recommendations/data` - Get SPK data

---

### 6. KEY CONTROLLERS & LOGIC

#### WorkoutController (`app/Http/Controllers/learn/WorkoutController.php`) - THE CORE EXAM ENGINE

This is the **most critical controller** for the exam system:

1. **`restart(Workout $workout)`**: Restarts a quiz attempt. Receives `used_dda` (bool) and `dda_difficulty` from request. Resets workout score/completion status, deletes old quiz logs, then calls `WorkoutService::setWorkOutQuizSyncForThisExcersice()` which selects questions based on difficulty. Redirects to the task view.

2. **`prepared(Participant, Sessionable)`**: First-time setup - creates a new Workout record via `WorkoutService::WorkOutSyncForThisExcersice()`.

3. **`task(Participant, Sessionable)`**: Uses `TaskFactory` to determine which adapter to render (Quiz, Document, File, etc.) and renders the task view. For quizzes, the `QuizAdapter` is used.

4. **`completedAndNext(Workout)`**: **THE DDA HEARTBEAT** - Called when a student completes/saves a quiz:
   - Recomputes score via `WorkoutService::recomputeScore()`
   - **Calculates topic levels** from actual question-by-question performance (accuracy per topic mapped to levels 0-4)
   - **Determines difficulty**: DDA difficulty from `workout.current_dda_difficulty`, non-DDA from log analysis
   - **Creates a `WorkoutRestartLog`** with full history: previous_score, dda_difficulty, non_dda_difficulty, topic_levels, payload (all quiz logs), used_dda flag
   - **First attempt always uses Non-DDA** (overrides to `used_dda = false`)
   - After saving history: resets workout with `used_dda = 1` (defaults to DDA for subsequent attempts)
   - Navigates to next sessionable activity

5. **`workout(Request)`**: AJAX endpoint - receives `question_id` + `workout_id`, uses `QuestionFactory` to build the appropriate question adapter (based on QuestionType), calls `workoutChecker()` to validate the answer, returns JSON result.

#### Analytics Controllers (DDA/RL related)

**DDAMetricsController** (`/panel/analytics/dda-metrics`): Evaluates DDA effectiveness by analyzing difficulty distribution (easy/medium/hard counts), success rates per difficulty, and overall effectiveness percentage. Recommends whether DDA is effective (>= 70% threshold).

**DDAComparisonController** (`/panel/analytics/dda-comparison`): Compares topic levels achieved under DDA vs Non-DDA modes across selected students. Aggregates topic levels from restart logs and displays averages.

**TopicLevelComparisonController** (`/mentor/topic-comparison`): Comprehensive analytics dashboard:
- Aggregates topic levels by mode (DDA, Non-DDA, overall)
- Per-quiz comparison
- **DDA Metrics**: difficulty distribution, success rates, effectiveness
- **SPK Recommendations**: Penilaian/Decision Support System that analyzes student performance to identify weak topics (< level 2), calculates average DDA vs Non-DDA scores, determines intervention level (high/medium/low), and generates suggested strategies

**SPKRecommendationController** (`/panel/analytics/spk-recommendations`): Standalone SPK recommendation engine that analyzes student logs for:
- Average DDA vs Non-DDA scores
- DDA improvement (difference)
- Weak topics identification (level < 2)
- Intervention level classification (weak topic count: >=3 high, >=1 medium, 0 low)
- Suggested strategies

#### Other Key Controllers

- **QuizController**: CRUD for quizzes, manages quiz-topic assignments, question ordering within quizzes
- **QuestionController**: CRUD for questions with authorization
- **SessionController**: Manages sessions and their polymorphic relationships (attach quizzes/files/documents/rubrics/feedbacks)
- **TermSessionController**: Attaches/detaches sessions to terms with ordering
- **GeneralController**: Dashboard

---

### 7. DDA (DYNAMIC DIFFICULTY ADJUSTMENT) & REINFORCEMENT LEARNING SYSTEM

The DDA system is implemented across multiple layers:

#### A. Database Layer

- **`questions.difficulty`** - `enum('easy', 'medium', 'hard')` on each question
- **`questions.topic`** - string topic tag on each question (e.g., "Algebra", "Geometry", "Calculus")
- **`quizzes.random_question`** - number of random questions to draw from bank
- **`quiz_topics`** - topics assigned to a quiz for filtering
- **`workouts.used_dda`** - boolean whether DDA is active for this workout
- **`workouts.current_dda_difficulty`** - current DDA difficulty level
- **`workout_restart_logs`** - complete history table:
  - `dda_difficulty` - difficulty used in DDA mode
  - `non_dda_difficulty` - difficulties used in non-DDA mode
  - `topic_levels` - JSON map of topic -> level (0-4)
  - `used_dda` - whether this attempt used DDA
  - `payload` - full quiz log data
  - `previous_score` - score before restart

#### B. WorkoutService Logic

**`setWorkOutQuizSyncForThisExcersice(Workout, Quiz, ?difficulty)`**:

1. If quiz has `random_question > 0`:
   - Queries questions filtered by quiz topics
   - If difficulty specified: **priority from recommended difficulty first**, supplement from other difficulties
   - If no difficulty: random selection
2. If quiz uses manually assigned questions:
   - Filters by quiz topics
   - Applies difficulty filter (with fallback if filtered set is empty)
   - Applies shuffle based on `quiz.is_shuffle`

#### C. Topic Level Calculation (in `completedAndNext`)

After each workout completion, topic levels are calculated:
- Group quiz logs by topic
- Calculate accuracy per topic: `(total_score / (count * 100)) * 100`
- Map to level 0-4:
  - >= 80% -> Level 4
  - >= 60% -> Level 3
  - >= 40% -> Level 2
  - > 0% -> Level 1
  - 0% -> Level 0

#### D. DDA Card (Frontend - `_dda_card.blade.php`)

The student review page shows two restart options:
1. **Restart with DDA** - sends quiz logs + topic levels to `http://127.0.0.1:8001/recommend` (FastAPI DDA service) for analysis, displays recommendation (next_difficulty, next_topic, recommended_topic_level with reasoning)
2. **Restart without DDA** - standard fixed difficulty

If the FastAPI service is offline, it defaults to "medium" difficulty.

#### E. FastAPI DDA Service (`fastapi-dda/`)

The directory contains a Python 3.12 virtual environment with:
- **FastAPI 0.128** - web framework
- **Uvicorn 0.40** - ASGI server
- **SQLModel 0.0.31** - SQL database ORM
- **Pydantic 2.12** - data validation
- **SQLAlchemy 2.0** - database toolkit

**However, no actual Python source files exist yet** in `fastapi-dda/src/`. The `__pycache__` directory exists but is empty of source. This means the FastAPI recommendation endpoint is **planned but not yet implemented**. The frontend calls `POST http://127.0.0.1:8001/recommend` but there is no Python code to handle it yet.

#### F. Frontend DDA Components

1. **`dda-topic-levels.js`** - Loads topic levels from `allRestartLogs` global JS variable and renders progress bars per topic (level 0-5 visual)
2. **`_topic_levels_card.blade.php`** - Card container for topic levels display
3. **`_dda_card.blade.php`** - Full DDA restart UI with analysis button
4. **`dda-metrics.blade.php`** - Admin dashboard with Chart.js charts for difficulty distribution and success rates
5. **`dda-comparison.blade.php`** - Admin comparison view (DDA vs Non-DDA topic level chart)
6. **`topic-levels-comparison.blade.php`** (two versions - admin and mentor) - Comprehensive analytics
7. **`spk-recommendation.blade.php`** - SPK recommendations dashboard

---

### 8. FRONTEND COMPONENTS RELATED TO EXAMS

| File | Purpose |
|---|---|
| `resources/views/contents/learn/quiz/show.blade.php` | Main quiz taking view - renders Livewire `activity.quiz` component with score display, save/close button |
| `resources/views/contents/learn/quiz/review.blade.php` | Quiz review after completion - shows score, stats, DDA restart options, topic levels, question-by-question review (Livewire `activity.result`), restart history |
| `resources/views/contents/learn/quiz/prepare.blade.php` | Pre-quiz preparation/start screen |
| `resources/views/contents/learn/quiz/_dda_card.blade.php` | DDA restart card with analysis (see section 7D) |
| `resources/views/contents/learn/quiz/_topic_levels_card.blade.php` | Topic levels progress card |
| `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php` | Learner-facing topic comparison |
| `resources/views/contents/admin/analytics/dda-metrics.blade.php` | Admin DDA metrics dashboard with Chart.js |
| `resources/views/contents/admin/analytics/dda-comparison.blade.php` | Admin DDA vs Non-DDA comparison |
| `resources/views/contents/admin/analytics/topic-levels-comparison.blade.php` | Admin topic levels analytics |
| `resources/views/contents/admin/analytics/spk-recommendation.blade.php` | Admin SPK recommendations |
| `resources/js/dda-topic-levels.js` | Topic level visualization JS |
| **Livewire Components:** | |
| `app/Http/Livewire/Activity/Quiz.php` | Livewire component for quiz interaction |
| `app/Http/Livewire/Activity/Result.php` | Livewire component for quiz result review |

---

### 9. AI/ML SERVICE CONFIGURATION

**There is no actual AI/ML service implemented yet.** The project has:

1. **An empty FastAPI scaffold** in `fastapi-dda/` with a full Python virtual environment ready for:
   - FastAPI web server
   - SQLModel/SQLAlchemy for database access
   - Uvicorn for serving
   - Pydantic for data validation

2. **A frontend call to the planned service** at `POST http://127.0.0.1:8001/recommend` (in `_dda_card.blade.php` line 130)

3. **Analytics controllers** that implement the "intelligence" in PHP:
   - Topic level calculation based on accuracy
   - DDA effectiveness metrics (threshold-based: >= 70% effective)
   - SPK (Sistem Pendukung Keputusan) - decision support using simple rule-based logic
   - The SPK algorithm: count weak topics (< level 2), classify intervention level (high: >=3, medium: 1-2, low: 0)

4. **No actual Reinforcement Learning, ML model, or external AI service** is currently operational. The "DDA" is a rule-based system:
   - Difficulty selection is passed from the frontend (chosen by student via DDA card analysis or default)
   - The FastAPI service was intended to provide RL-based recommendations but has no Python source files

---

### EXAM SYSTEM WORKFLOW SUMMARY

1. **Admin creates** courses, terms, sessions, quizzes, and questions (with difficulty + topic tags)
2. **Quiz can be configured** with `random_question` (draw from bank) or manual question assignment, plus topic filtering
3. **Students enroll** in terms via `term_user` pivot with role_id = student
4. **When a student accesses a quiz**: `WorkoutController::task` -> `QuizAdapter::Render` -> either shows prepare screen (first time) or the quiz
5. **Quiz takes questions** from the configured pool, optionally filtered by difficulty (if DDA) or topics
6. **During the quiz**: questions are displayed via Livewire (`activity.quiz`), answers submitted via `POST /learn/quiz/workout` (AJAX), scored per question
7. **On completion**: `WorkoutController::completedAndNext` calculates score, computes topic levels from accuracy, saves restart history, and redirects to next activity
8. **On review page**: students can choose to **restart with DDA** (calls FastAPI for recommendation, fallback to medium) or **restart without DDA** (standard)
9. **Mentors/Admins** can analyze performance via DDA metrics, topic level comparisons, and SPK recommendations dashboards
10. **First attempt always uses Non-DDA mode** as a baseline; subsequent attempts default to DDA mode

---

### KEY FILE PATHS (Absolute)

**Critical Core:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Http\Controllers\learn\WorkoutController.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Workout\WorkoutService.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\QuestionFactory.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Modules\Tasks\TaskFactory.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Modules\Tasks\Adabpter\QuizAdapter.php`

**Models:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\Workout.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\WorkoutQuizLog.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\WorkoutRestartLog.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\Quiz.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\Question.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\QuizQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\QuizTopic.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\Participant.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Models\Sessionable.php`

**Analytics/DDA Controllers:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Http\Controllers\Analytics\DDAMetricsController.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Http\Controllers\Analytics\DDAComparisonController.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Http\Controllers\Analytics\TopicLevelComparisonController.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Http\Controllers\Analytics\SPKRecommendationController.php`

**DDA Migrations (chronological):**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_01_30_164944_add_difficulty_to_questions_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_02_03_200000_create_workout_restart_logs_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_03_12_065113_add_topic_to_questions_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_03_12_211042_add_non_dda_difficulty_to_workout_restart_logs_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_03_12_214614_add_used_dda_to_workouts_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_04_20_230402_add_topic_levels_and_used_dda_to_workout_restart_logs_table.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\database\migrations\2026_04_20_create_quiz_topics_table.php`

**Frontend DDA/Exam Views:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\learn\quiz\show.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\learn\quiz\review.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\learn\quiz\_dda_card.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\learn\quiz\_topic_levels_card.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\admin\analytics\dda-metrics.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\admin\analytics\dda-comparison.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\admin\analytics\topic-levels-comparison.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\views\contents\admin\analytics\spk-recommendation.blade.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\resources\js\dda-topic-levels.js`

**Routes:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\routes\web.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\routes\api.php`

**Question Type Adapters:**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\MultipleQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\EssayQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\TrueFalseQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\ShortAnswerQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\TestQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\MatchingCaseQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\UploadFileQuestion.php`
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\app\Utility\Question\Adabpter\VoiceRecordQuestion.php`

**FastAPI (scaffold only, no source code):**
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\fastapi-dda\.venv\` (Python 3.12 venv with packages)
- `C:\KULIAH D4 LANJUT JENJANG\PA\laramint\fastapi-dda\src\` (empty - awaiting implementation)

---

### ARCHITECTURE DIAGRAM (Text)

```
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL 9 BACKEND                           │
│                                                                 │
│  ┌─────────────┐   ┌──────────────┐   ┌──────────────────────┐  │
│  │ Controllers  │──▶│  Services    │──▶│    Models/DB         │  │
│  │ (Quiz,       │   │  (Workout,   │   │  (30 Eloquent models) │  │
│  │  Workout,    │   │   DDA, SPK)  │   │                      │  │
│  │  Analytics)  │   │              │   │  MySQL "mint" DB     │  │
│  └──────┬───────┘   └──────────────┘   └──────────────────────┘  │
│         │                                                         │
│  ┌──────▼───────┐   ┌──────────────────┐                         │
│  │  Livewire     │   │  Task/Question   │                        │
│  │  Components   │   │  Adapters        │                        │
│  │  (Quiz,       │   │  (Factory Pattern)│                       │
│  │   Result)     │   │                  │                        │
│  └──────┬───────┘   └──────────────────┘                         │
└─────────┼───────────────────────────────────────────────────────┘
          │                          ▲
          │ POST /learn/quiz/workout │ DDA Recommendation
          │ (AJAX)                   │ (call to 127.0.0.1:8001)
          ▼                          │
┌─────────────────────┐    ┌──────────────────────────┐
│  FRONTEND (Blade +  │    │  FastAPI DDA Microservice │
│  JS/Livewire)       │    │  (PLANNED - NOT YET      │
│                     │    │   implemented)            │
│  - Quiz taking      │    │                          │
│  - Review + DDA     │    │  POST /recommend         │
│  - Analytics Charts │    │  ├─ next_difficulty      │
│  - Topic Levels     │    │  ├─ next_topic           │
│  - SPK Dashboard    │    │  └─ recommended_level    │
└─────────────────────┘    └──────────────────────────┘
```

The project is a comprehensive **LMS (Learning Management System)** with an exam/quiz engine that implements a **rule-based Dynamic Difficulty Adjustment (DDA)** system. The DDA tracks per-topic student performance across multiple restart attempts (saving full history), and provides analytics dashboards for mentors/admins to compare DDA vs non-DDA effectiveness. A planned FastAPI microservice for reinforcement learning-based difficulty recommendations has its environment set up but the Python source code has not been written yet.
</task_result>
</task>
```

**Tool: task**

**Input:**
```json
{
  "description": "Explore dda_service project",
  "prompt": "Explore the project at C:\\KULIAH D4 LANJUT JENJANG\\PA\\dda_service thoroughly. I need to understand:\n\n1. The overall project structure\n2. What technologies/frameworks are used (Python? Flask? FastAPI? TensorFlow?)\n3. All Python files - read them completely\n4. Any Reinforcement Learning models or algorithms\n5. API endpoints and how they work\n6. How it communicates with the main Laravel application\n7. Configuration files\n8. Requirements/dependencies\n\nRead ALL Python files, requirements.txt, config files, and any documentation.\n\nReturn a comprehensive analysis of:\n- Project structure\n- How the DDA/RL algorithm works\n- API endpoints and their parameters\n- How it integrates with the Laravel app",
  "subagent_type": "explore"
}
```

**Output:**
```
<task id="ses_1341fa40affecx5ExAG9PGNasf" state="completed">
<task_result>
Now I have a thorough understanding of the entire project. Here is my comprehensive analysis:

---

# Comprehensive Analysis: DDA Service (Dynamic Difficulty Adjustment)

## 1. Project Structure

```
dda_service/
├── .git/
├── .venv/                          # Python virtual environment (venv)
├── venv/                           # Python virtual environment (venv)
├── __pycache__/
├── README.md                       # Project documentation
├── requirements.txt                # Python dependencies
├── run_recommend.py                # Test script to send a sample request
└── app/                            # Main application package
    ├── __init__.py                 # Empty init (package marker)
    ├── __pycache__/
    ├── main.py                     # FastAPI application + endpoints
    ├── main.py.bak                 # Earlier version of main.py
    ├── main.py.backup              # Another backup (intermediate version)
    ├── models.py                   # Pydantic data models / schemas
    ├── models.py.backup            # Earlier version of models.py
    ├── rl_agent.py                 # Q-learning RL Agent
    └── rl_agent.py.backup          # Earlier simpler version
```

## 2. Technologies & Frameworks

| Technology | Purpose |
|---|---|
| **Python 3.12** | Runtime |
| **FastAPI** | Web framework for REST API microservice |
| **Uvicorn** | ASGI server (runs the FastAPI app) |
| **Pydantic** | Request/response data validation and serialization |
| **NumPy** | Numerical computing for Q-table and state calculations |
| **Pandas** | Data analysis for the `/analyze` endpoint (DataFrame) |
| **Matplotlib** | Chart generation for the `/analyze` endpoint (pie charts, time series, base64 images) |
| **Seaborn** | Listed in requirements but NOT used in code |

There is **no TensorFlow**, **no PyTorch**, and **no neural networks**. The RL is implemented purely with a NumPy-based **Q-table** (tabular Q-learning).

---

## 3. All Python Files -- Complete Contents

### 3.1 `requirements.txt`
```
fastapi
uvicorn
pydantic
numpy
pandas
matplotlib
seaborn
```

### 3.2 `app/__init__.py`
Empty file -- simply marks `app/` as a Python package.

### 3.3 `app/models.py` (Current version -- 48 lines)

Defines six Pydantic models:

| Model | Fields | Purpose |
|---|---|---|
| **`ExamLog`** | `question_id: int`, `difficulty: str`, `is_correct: bool`, `topic: str`, `answer_time_seconds: Optional[float]` | One log entry from a student's answer |
| **`DDAInput`** | `user_id: str`, `session_logs: List[ExamLog]`, `topic_levels: Optional[Dict[str, int]]` | Request body for `/recommend` |
| **`DDAResponse`** | `next_difficulty: str`, `next_topic: str`, `recommended_topic_level: Optional[int]` | Response from `/recommend` |
| **`RestartLog`** | `created_at: str`, `dda_difficulty`, `non_dda_difficulty`, `previous_score`, `payload`, `used_dda`, `dda_topic`, `non_dda_topic`, `topic_levels` | A restart attempt record for analytics |
| **`AnalysisInput`** | `workout_id: Optional[int]`, `restart_logs: List[RestartLog]` | Request body for `/analyze` |
| **`AnalysisResponse`** | `summary: dict`, `graphs: dict` | Response from `/analyze` with stats + base64 chart images |

**Key evolution from backup**: The original `DDAInput` had no `topic_levels` field; `DDAResponse` had no `recommended_topic_level`; `RestartLog` originally lacked `dda_topic`, `non_dda_topic`, and `topic_levels`. These were added for topic-aware DDA.

### 3.4 `app/rl_agent.py` (Current version -- 127 lines)

#### RLAgent Class -- Q-Learning Implementation

**Constructor:**
```python
def __init__(self, learning_rate=0.1, discount_factor=0.9, exploration_rate=0.1):
```
- `lr` (learning rate alpha) = 0.1
- `gamma` (discount factor) = 0.9
- `epsilon` (exploration rate) = 0.1

**Q-Table:** `np.zeros((4, 5, 3))`
- Dimension 1: **accuracy state** (4 buckets: 0-25%, 25-50%, 50-75%, 75-100%)
- Dimension 2: **topic level state** (5 levels: 0, 1, 2, 3, 4)
- Dimension 3: **actions** (3: 'easy', 'medium', 'hard')
- Total: 4 x 5 x 3 = 60 Q-values

**Key Methods:**

1. **`_get_accuracy_state(dda_input)`** -- Returns 0-3 based on overall session accuracy:
   - 0: accuracy <= 25%
   - 1: accuracy <= 50%
   - 2: accuracy <= 75%
   - 3: accuracy > 75%

2. **`_get_topic_level_state(dda_input, topic)`** -- Returns 0-4:
   - If a specific topic is given and exists in `topic_levels`, use that level (capped at 4).
   - Otherwise, use the average of all topic levels.
   - Returns 0 if no `topic_levels` data.

3. **`_get_combined_state(dda_input, topic)`** -- Returns `(accuracy_state, topic_level_state)` tuple.

4. **`choose_action(dda_input, topic)`** -- Epsilon-greedy action selection:
   - With probability `epsilon` (10%): random action (exploration).
   - Otherwise: argmax over Q-values for the current state (exploitation).
   - Returns 'easy', 'medium', or 'hard'.

5. **`update_q_table(dda_input, topic)`** -- The core Q-learning update:
   - Extracts the last answer log.
   - **Reward function**: `+1` for correct, `-1` for incorrect.
   - **Topic bonus**: If the topic has a level > 0, adds `0.2 * (topic_level - 1)` as a small bonus for higher-level topics.
   - Computes previous state (excluding last log) and current state.
   - Uses the standard Q-learning Bellman equation:
     ```
     new_value = (1 - lr) * old_value + lr * (reward + gamma * max(next_Q))
     ```

6. **`get_topic_recommendation(dda_input, current_topic)`** -- Returns `(difficulty, topic_level_insight)`.

**Global Instance:** `agent = RLAgent()` -- a singleton used by main.py.

#### Evolution from backup (rl_agent.py.backup):
- Original had a 1D state (accuracy only): Q-table was `(4, 3)`.
- Current version has a 2D state (accuracy + topic level): Q-table is `(4, 5, 3)`.
- Original had no `_get_topic_level_state`, `_get_combined_state`, or `get_topic_recommendation`.
- Original reward was simply `+1/-1` with no topic bonus.

### 3.5 `app/main.py` (Current version -- 235 lines)

This is the FastAPI application. It has **4 endpoints**:

---

#### Endpoint 1: `POST /recommend`

```python
@app.post("/recommend", response_model=DDAResponse)
async def get_recommendation(dda_input: DDAInput):
```

**Input (JSON body -- DDAInput):**
```json
{
    "user_id": "student_123",
    "session_logs": [
        {"question_id": 1, "difficulty": "easy", "is_correct": true, "topic": "algebra", "answer_time_seconds": 12.3},
        {"question_id": 2, "difficulty": "medium", "is_correct": false, "topic": "algebra", "answer_time_seconds": 25.0},
        {"question_id": 3, "difficulty": "medium", "is_correct": true, "topic": "geometry", "answer_time_seconds": 18.7}
    ],
    "topic_levels": {"algebra": 2, "geometry": 1}
}
```

**Processing flow:**
1. Calls `agent.update_q_table(dda_input)` -- the RL agent learns from the last answer.
2. Calls `_select_next_topic(dda_input)` -- deterministic topic selection:
   - If `topic_levels` exist: pick the topic with the **lowest level** (weakest topic).
   - Otherwise: pick the topic with the **lowest accuracy** from `session_logs`.
   - Fallback: last attempted topic or "general".
3. Calls `agent.choose_action(dda_input, next_topic)` -- RL agent picks difficulty based on combined (accuracy, topic_level) state.
4. Calls `agent.get_topic_recommendation(dda_input, next_topic)` -- gets the recommended topic level insight.

**Output (JSON -- DDAResponse):**
```json
{
    "next_difficulty": "easy",
    "next_topic": "algebra",
    "recommended_topic_level": 2
}
```

---

#### Endpoint 2: `POST /analyze`

```python
@app.post("/analyze", response_model=AnalysisResponse)
async def analyze(input: AnalysisInput):
```

**Input (JSON body -- AnalysisInput):**
```json
{
    "workout_id": 123,
    "restart_logs": [
        {
            "created_at": "2026-06-15T10:00:00Z",
            "dda_difficulty": "medium",
            "non_dda_difficulty": null,
            "previous_score": 65.0,
            "payload": [],
            "used_dda": true,
            "dda_topic": "algebra",
            "non_dda_topic": null,
            "topic_levels": {"algebra": 2, "geometry": 1}
        }
    ]
}
```

**Processing flow:**
1. Converts restart logs to a pandas DataFrame.
2. For each log, determines which difficulty/topic to use based on `used_dda` flag.
3. Computes summary statistics:
   - `total_restarts`, `by_difficulty`, `by_topic`
   - `avg_topic_level`, `max_topic_level`
   - `avg_previous_score`, `avg_previous_score_by_difficulty`
   - `used_dda_rate`
4. Generates base64-encoded PNG graphs:
   - Pie chart: difficulty distribution
   - Pie chart: topic distribution
   - Line chart: previous score over time

**Output (JSON -- AnalysisResponse):**
```json
{
    "summary": {
        "total_restarts": 5,
        "by_difficulty": {"easy": 2, "medium": 2, "hard": 1},
        "by_topic": {"algebra": 3, "geometry": 2},
        "avg_topic_level": 1.8,
        "max_topic_level": 3,
        "avg_previous_score": 72.5,
        "avg_previous_score_by_difficulty": {"easy": 80.0, "medium": 65.0, "hard": 70.0},
        "used_dda_rate": 0.6
    },
    "graphs": {
        "difficulty_pie": "data:image/png;base64,...",
        "topic_pie": "data:image/png;base64,...",
        "score_timeseries": "data:image/png;base64,..."
    }
}
```

---

#### Endpoint 3: `GET /`

```python
@app.get("/")
async def root():
```
Returns: `{"message": "DDA Service is running. Use the /recommend endpoint to get recommendations."}`

---

#### Endpoint 4: `POST /admin/clear`

```python
@app.post('/admin/clear')
async def clear_data(clear_q_table: bool = True):
```
- Admin endpoint to **reset the RL agent's Q-table** to all zeros (in-memory only).
- Does NOT affect any Laravel database records.
- Returns: `{"cleared": {"q_table": true}}`

---

**CORS Configuration:**
```python
allow_origins=["http://127.0.0.1:8000", "http://localhost:8000"]
```
The Laravel app runs on port 8000, DDA service on port 8001.

---

### 3.6 `run_recommend.py`

Simple test script that sends a POST request to `http://127.0.0.1:8001/recommend` with a hardcoded sample payload and prints the response.

---

## 4. How the DDA / RL Algorithm Works

### State Representation
The state is a 2D tuple `(accuracy_bucket, topic_level)`:
- **Accuracy bucket** (0-3): calculated from the ratio of correct answers in the session.
- **Topic level** (0-4): pre-calculated by the Laravel backend based on per-topic accuracy.

### Actions
Three possible difficulties: `easy`, `medium`, `hard`.

### Reward Function
```
reward = (+1 if correct else -1) + (0.2 * (topic_level - 1) if topic_level > 0)
```
The topic bonus incentivizes the agent to maintain progress on higher-level topics.

### Learning Algorithm
Standard **tabular Q-learning**:
```
Q(s,a) <- (1 - lr) * Q(s,a) + lr * (reward + gamma * max(Q(s', a')))
```
- Previous state is computed from all logs *except* the last one.
- Current state is computed from all logs including the last one.
- The "next max" Q-value is taken from the current state.

### Exploration vs Exploitation
Epsilon-greedy with `epsilon = 0.1` (10% random exploration).

### Topic Selection (Deterministic)
The `_select_next_topic` function in `main.py` chooses the **weakest topic** (lowest level or lowest accuracy). This is NOT part of the RL -- it is a deterministic heuristic.

---

## 5. API Endpoints Summary

| Method | Path | Input | Output | Purpose |
|---|---|---|---|---|
| `GET` | `/` | None | `{"message": "..."}` | Health check |
| `POST` | `/recommend` | `DDAInput` (JSON) | `DDAResponse` (JSON) | Get next difficulty + topic recommendation |
| `POST` | `/analyze` | `AnalysisInput` (JSON) | `AnalysisResponse` (JSON + base64 images) | Get analytics on restart history |
| `POST` | `/admin/clear` | Query param `clear_q_table` | `{"cleared": {"q_table": true}}` | Reset Q-table |

**Server runs on:** `http://127.0.0.1:8001`

---

## 6. Integration with the Laravel Application (laramint)

The integration is a **microservice pattern** -- the DDA service runs as a separate Python process and the Laravel PHP app communicates via HTTP.

### 6.1 Student Quiz Workflow

1. **Student attempts a quiz** -- The Laravel `WorkoutController` records answers in `workout_quiz` table.

2. **Student completes** -- `WorkoutController::completedAndNext()` processes the attempt:
   - Calculates per-topic accuracy and maps to topic levels (0-4 scale).
   - Saves a `WorkoutRestartLog` record with `dda_difficulty`, `non_dda_difficulty`, `topic_levels` (JSON), `used_dda` flag, and `previous_score`.
   - Resets the workout for the next attempt.

3. **Student can restart** via the review page (`_dda_card.blade.php`):
   - The Blade template sends a **client-side AJAX POST** to `http://127.0.0.1:8001/recommend` from the browser.
   - Payload includes: `user_id`, `session_logs` (mapped from the student's answers with difficulty, topic, correctness), and `topic_levels` (computed in PHP).
   - The DDA service returns `next_difficulty`, `next_topic`, `recommended_topic_level`.
   - The JavaScript displays the recommendation and creates a form with hidden `dda_difficulty` input.
   - When the student clicks "Apply & Restart", the form submits to `WorkoutController::restart()`.

4. **`WorkoutController::restart()`** receives:
   - `used_dda` (1 or 0)
   - `dda_difficulty` (the recommended difficulty)
   - It updates the workout record, deletes old quiz attempts, regenerates new questions at the specified difficulty (via `WorkoutService::setWorkOutQuizSyncForThisExcersice`), and redirects the student to the new attempt.

### 6.2 Database Tables Involved (Laravel side)

| Table | Role |
|---|---|
| `workouts` | Stores `used_dda` boolean and `current_dda_difficulty` |
| `workout_restart_logs` | Stores history of each restart: `dda_difficulty`, `non_dda_difficulty`, `previous_score`, `topic_levels` (JSON), `used_dda` |
| `workout_quiz` | Individual question-answer records (linked to `questions`) |
| `questions` | Has `difficulty` and `topic` fields used by the DDA service |

### 6.3 Analytics Pages (Admin/Mentor)

Several analytics controllers consume `workout_restart_logs` data:

| Controller | Route | Purpose |
|---|---|---|
| `DDAMetricsController` | `/admin/analytics/dda-metrics` | Evaluates DDA effectiveness (success rates per difficulty, overall effectiveness %) |
| `DDAComparisonController` | `/admin/analytics/dda-comparison` | Compares topic levels between DDA and Non-DDA modes |
| `TopicLevelComparisonController` | `/mentor/topic-comparison` | Detailed topic level comparison with heatmaps and charts |
| `SPKRecommendationController` | `/admin/analytics/spk-recommendation` | SPK (Sistem Pendukung Keputusan) recommendations based on DDA/Non-DDA performance |

These analytics use the `WorkoutRestartLog` model to aggregate statistics -- they do NOT call the Python DDA service directly.

### 6.4 First Attempt Treatment

The first attempt of any quiz is **always forced to Non-DDA** (`$usedDDA = false` in `completedAndNext()`). This establishes a baseline performance. Subsequent attempts default to DDA mode.

---

## 7. Data Flow Summary

```
[Browser/Student]
    |
    | (completes quiz)
    v
[Laravel: completedAndNext()]
    |-- Calculates topic_levels from per-topic accuracy
    |-- Saves WorkoutRestartLog (difficulty, topic_levels, score, used_dda)
    |-- Resets workout for next attempt
    |
    | (student clicks "Analyze & Restart")
    v
[Browser JS: _dda_card.blade.php]
    |-- Maps workout_quiz data to ExamLog format
    |-- POST http://127.0.0.1:8001/recommend
    |
    v
[Python DDA Service (FastAPI)]
    |-- agent.update_q_table() -> learns from last answer
    |-- _select_next_topic() -> picks weakest topic
    |-- agent.choose_action() -> picks difficulty via Q-learning
    |-- Returns {next_difficulty, next_topic, recommended_topic_level}
    |
    v
[Browser JS]
    |-- Displays recommendation with reasoning
    |-- Hidden form submits to Laravel
    |
    v
[Laravel: restart()]
    |-- Regenerates questions at recommended difficulty
    |-- Redirects to new attempt
```

---

## 8. Key Architectural Points

- **Stateless per request**: The DDA service receives the full history (`session_logs`) with every request. It does NOT maintain per-user session state -- the Q-table is global and shared across all users.
- **Global Q-table**: The single `agent` instance learns from ALL users' data, creating a generalized difficulty adjustment policy.
- **Topic awareness**: The system doesn't just adjust difficulty globally; it identifies the student's weakest topic and recommends focused practice on that topic.
- **First attempt baseline**: The first quiz attempt is always Non-DDA to establish a baseline, and subsequent attempts use DDA.
- **Analytics purely in Laravel**: All analytics/metrics pages query the `workout_restart_logs` table directly via Eloquent -- they do not depend on the Python service being running. The Python `/analyze` endpoint provides the same kind of analytics on-demand but is consumed by the JavaScript in the review page.

---

## 9. Configuration & Dependencies

**No `.env` or config files** in the Python service -- everything is hardcoded:
- Port: **8001**
- CORS origins: `http://127.0.0.1:8000`, `http://localhost:8000`
- RL hyperparameters: `lr=0.1`, `gamma=0.9`, `epsilon=0.1`
- Q-table dimensions: `4 x 5 x 3`

**Laravel integration relies on:**
- The Python service must be running on port 8001.
- The JavaScript fetch URL is hardcoded as `http://127.0.0.1:8001/recommend` in `_dda_card.blade.php`.
- No authentication/API keys between Laravel and the DDA service (internal network communication).
</task_result>
</task>
```

---

## Assistant (Build · DeepSeek V4 Flash Free · 17.3s)

_Thinking:_

Now I have comprehensive analysis of both projects. Let me provide a clear, structured summary to the user. They asked to analyze their code and the DDA service.

Berikut analisis lengkap dari kedua proyek Anda:

---

## 📁 **laramint** (Laravel 9 - PHP Backend)

**Framework:** Laravel 9 + Livewire 2 + MySQL

**Arsitektur:** LMS (Learning Management System) dengan mesin ujian kustom.

### Alur Ujian:
1. **Admin** membuat Course → Term → Session → Quiz → Questions (dengan `difficulty` & `topic`)
2. **Mahasiswa** mengerjakan quiz → `WorkoutController::task()` menampilkan soal via Livewire
3. **Submit jawaban** → `POST /learn/quiz/workout` → `QuestionFactory::workoutChecker()` memvalidasi
4. **Selesai** → `WorkoutController::completedAndNext()`:
   - Hitung topic levels (0-4) per topik berdasarkan akurasi
   - Simpan ke `workout_restart_logs` (riwayat lengkap: difficulty, topic_levels, score, used_dda)
   - Reset workout untuk attempt berikutnya
   - **Attempt pertama SELALU Non-DDA** (baseline)
5. **Review & Restart** → `_dda_card.blade.php` panggil `http://127.0.0.1:8001/recommend` → dapat rekomendasi difficulty + topik → restart dengan soal baru

### Database Kunci:
| Tabel | Fungsi |
|---|---|
| `questions` | `difficulty` (easy/medium/hard), `topic` (string) |
| `quiz_topics` | Topik yg dipilih untuk suatu quiz |
| `workouts` | `used_dda`, `current_dda_difficulty` |
| `workout_quiz_logs` | Jawaban per soal |
| `workout_restart_logs` | Riwayat restart: `dda_difficulty`, `non_dda_difficulty`, `topic_levels` (JSON), `used_dda`, `previous_score` |

### Fitur DDA (Sisi PHP - Rule-based):
- **Topic Level**: 0-4 berdasarkan akurasi per topik (≥80% → Lv4, ≥60% → Lv3, dst)
- **DDAMetricsController**: Evaluasi efektivitas DDA (threshold 70%)
- **SPKRecommendationController**: Decision support - hitung weak topics (< level 2), klasifikasi intervensi

---

## 🐍 **dda_service** (FastAPI - Python Microservice)

**Lokasi:** `C:\KULIAH D4 LANJUT JENJANG\PA\dda_service`

**Struktur:**
```
dda_service/
├── app/
│   ├── main.py              # FastAPI app (4 endpoints)
│   ├── models.py            # Pydantic schemas
│   └── rl_agent.py          # Q-learning RL Agent
├── requirements.txt
├── run_recommend.py         # Test script
└── README.md
```

### Endpoints API:

| Method | Path | Fungsi |
|---|---|---|
| `GET` | `/` | Health check |
| `POST` | `/recommend` | **Rekomendasi difficulty + topik berikutnya** |
| `POST` | `/analyze` | Analisis statistik + grafik restart logs |
| `POST` | `/admin/clear` | Reset Q-table |

### Algoritma RL (`rl_agent.py`):

**State:** 2D tuple `(accuracy_bucket, topic_level)`
- Accuracy bucket: 0-3 (0: ≤25%, 1: ≤50%, 2: ≤75%, 3: >75%)
- Topic level: 0-4 (dari Laravel)

**Action:** 3 pilihan difficulty - `easy`, `medium`, `hard`

**Reward:**
```
reward = (+1 jika benar else -1) + (0.2 * (topic_level - 1) jika topic_level > 0)
```

**Q-Learning Bellman:**
```
Q(s,a) ← (1-lr)*Q(s,a) + lr*(reward + γ * max(Q(s',a')))
```
- `lr=0.1`, `γ=0.9`, `ε=0.1`
- Q-table: `(4, 5, 3)` = 60 nilai
- Epsilon-greedy: 10% eksplorasi, 90% eksploitasi

**Pemilihan Topik:** Deterministik - pilih topik **terlemah** (lowest topic_level atau lowest accuracy)

### Integrasi dengan Laravel:
- Browser JS → `POST http://127.0.0.1:8001/recommend` → DDA Service → return `{next_difficulty, next_topic, recommended_topic_level}` → form restart
- **Q-table global** (shared semua user) - tidak ada session per-user
- **Tidak ada auth** antara Laravel & DDA service (internal network)

---

## 🔍 Temuan Penting

| Aspek | Status |
|---|---|
| **DDA Service** | ✅ Sudah berjalan (`uvicorn app.main:app --port 8001`) |
| **RL Algorithm** | ✅ Q-learning dengan state (accuracy, topic_level) |
| **Frontend Integration** | ✅ `_dda_card.blade.php` panggil API dari browser |
| **Analytics Dashboard** | ✅ DDA Metrics, Comparison, SPK |
| **FastAPI di laramint** | ❌ Hanya scaffold kosong (`fastapi-dda/` tanpa source) |
| **Persistensi Q-table** | ❌ In-memory saja (hilang saat restart service) |

**Rekomendasi:** Q-table perlu disimpan ke file/DB agar tidak hilang, dan kode FastAPI di `fastapi-dda/` dalam laramint bisa dihapus karena service sesungguhnya ada di `dda_service/`.

---

