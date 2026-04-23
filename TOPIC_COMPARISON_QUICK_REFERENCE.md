# Topic Comparison Feature - Quick Reference & Code Snippets

## 🔍 Quick Reference for Developers

### Access Points

#### Main Page URL
```
https://yourapp.com/mentor/topic-comparison
```

#### API Endpoint
```
GET https://yourapp.com/mentor/topic-comparison/data
```

#### Session-Specific Endpoint
```
GET https://yourapp.com/mentor/topic-comparison/session/{sessionId}
```

---

## 📝 Code Snippets for Integration

### 1. Add Link in Navigation Menu

**Location**: `resources/views/layouts/` (your nav/sidebar file)

```blade
<!-- Topic Comparison Link -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('topicComparison') }}">
        <i class="fas fa-chart-bar me-2"></i> Topic Comparison
    </a>
</li>
```

### 2. Add to Mentor Dashboard

**Location**: `resources/views/contents/dashboard/` or similar

```blade
<div class="col-12 col-md-6 col-lg-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="text-info font-weight-bold text-uppercase mb-1">
                <i class="fas fa-chart-bar me-2"></i> Analytics
            </div>
            <div class="h3 mb-0">Topic Comparison</div>
        </div>
        <a href="{{ route('topicComparison') }}" class="btn btn-info btn-sm mt-2 ms-3">
            View Report
        </a>
    </div>
</div>
```

### 3. Create Menu Entry in Admin Panel

**File**: `config/menu.php` (if applicable)

```php
[
    'title' => 'Topic Comparison',
    'icon' => 'fas fa-chart-bar',
    'url' => 'topicComparison',
    'permission' => 'mentor.list',
    'order' => 15
]
```

### 4. Add to Mentors Dropdown

**Location**: Header/navbar mentors menu

```blade
<li>
    <a class="dropdown-item" href="{{ route('topicComparison') }}">
        <i class="fas fa-chart-bar me-2"></i> 
        Topic Level Comparison
        <span class="badge bg-success ms-2">New</span>
    </a>
</li>
```

---

## 🔌 API Usage Examples

### JavaScript / Fetch
```javascript
// Get comparison data
fetch('/mentor/topic-comparison/data')
    .then(res => res.json())
    .then(data => {
        console.log('DDA Data:', data.dda_data);
        console.log('Non-DDA Data:', data.nondda_data);
        console.log('Average Stats:', data.stats.dda_avg);
    });
```

### cURL
```bash
curl -X GET "https://yourapp.com/mentor/topic-comparison/data" \
  -H "Cookie: XSRF-TOKEN=<your-token>" \
  -H "Accept: application/json"
```

### jQuery (Legacy)
```javascript
$.ajax({
    url: '/mentor/topic-comparison/data',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        if (data.success) {
            console.log('Students:', data.students);
            console.log('Topics:', data.topics);
        }
    }
});
```

---

## 🛠️ Customization Snippets

### Custom Level Thresholds

**File**: `app/Http/Controllers/TopicLevelComparisonController.php`
**Method**: `calculateTopicStats()`

```php
// Change these values to customize thresholds
if ($accuracy >= 90) {          // Was 80
    $topicLevels[$topic] = 4;
} elseif ($accuracy >= 75) {    // Was 60
    $topicLevels[$topic] = 3;
} elseif ($accuracy >= 50) {    // Was 40
    $topicLevels[$topic] = 2;
} elseif ($accuracy > 0) {
    $topicLevels[$topic] = 1;
} else {
    $topicLevels[$topic] = 0;
}
```

### Custom Colors

**File**: `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php`
**Section**: `@push('styles')`

```css
/* Change primary color */
.bg-gradient-primary {
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E72 100%) !important;
}

/* Change active nav color */
.nav-tabs .nav-link.active {
    color: #FF6B6B !important;
    border-bottom-color: #FF6B6B !important;
}

/* Change legend badge colors */
.badge.bg-success { background-color: #00D084 !important; }
.badge.bg-info { background-color: #0084FF !important; }
.badge.bg-warning { background-color: #FF9500 !important; }
.badge.bg-secondary { background-color: #6C757D !important; }
```

### Add Export Button

**File**: `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php`
**Location**: In the header card

```blade
<x-BackButton />
<button class="btn btn-outline-primary btn-sm ms-2" onclick="exportToCSV()">
    <i class="fas fa-download me-1"></i> Export CSV
</button>
```

**JavaScript**:
```javascript
function exportToCSV() {
    // Simple CSV export of heatmap data
    let csv = 'Student,Topic1,Topic2,Topic3\n';
    // Add your export logic here
    
    let link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    link.target = '_blank';
    link.download = 'topic_comparison_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}
```

---

## 🔍 Query Examples for Debugging

### Get DDA Students
```php
$ddaStudents = User::role('student')
    ->whereHas('Workouts', function ($q) {
        $q->whereHas('RestartLogs', function ($r) {
            $r->where('used_dda', true);
        });
    })
    ->get();
```

### Get Non-DDA Students
```php
$nonddaStudents = User::role('student')
    ->whereHas('Workouts', function ($q) {
        $q->whereHas('RestartLogs', function ($r) {
            $r->where('used_dda', false);
        });
    })
    ->get();
```

### Get All Topics
```php
$topics = Question::whereNotNull('topic')
    ->distinct('topic')
    ->pluck('topic')
    ->sort();
```

### Get Topic Levels for Student
```php
$student = User::find($studentId);
$topicLevels = [];

foreach ($student->Workouts as $workout) {
    foreach ($workout->WorkOutQuiz as $log) {
        $topic = $log->Question->topic;
        // Calculate and aggregate...
    }
}
```

---

## 📊 Database Queries Reference

### Check RestartLogs Structure
```sql
SELECT * FROM workout_restart_logs 
WHERE id = 1;
```

### Check WorkoutQuizLog Structure
```sql
SELECT * FROM workout_quiz_logs 
WHERE id = 1;
```

### Check Question Topics
```sql
SELECT DISTINCT topic FROM questions 
WHERE topic IS NOT NULL 
ORDER BY topic;
```

### Count Students by Mode
```sql
SELECT 
    CASE 
        WHEN wrl.used_dda = 1 THEN 'DDA'
        WHEN wrl.used_dda = 0 THEN 'Non-DDA'
    END as mode,
    COUNT(DISTINCT u.id) as student_count
FROM workout_restart_logs wrl
JOIN workouts w ON wrl.workout_id = w.id
JOIN users u ON w.user_id = u.id
WHERE u.id IN (SELECT user_id FROM model_has_roles WHERE role_id = 2)
GROUP BY wrl.used_dda;
```

---

## 🧪 Testing with Sample Data

### Create Sample Student Data
```php
// In a seeder or tinker
$student = User::factory()
    ->hasAttached(Role::where('name', 'student')->first())
    ->create(['name' => 'Test Student']);

$participant = Participant::create([
    'user_id' => $student->id,
    'term_id' => 1
]);

$sessionable = Sessionable::first();

$workout = Workout::create([
    'user_id' => $student->id,
    'participant_id' => $participant->id,
    'sessionable_id' => $sessionable->id,
    'is_completed' => 1,
    'score' => 75,
    'used_dda' => false
]);

// Create quiz logs
$questions = Question::where('topic', 'Algorithm')->limit(5)->get();
foreach ($questions as $q) {
    WorkoutQuizLog::create([
        'workout_id' => $workout->id,
        'question_id' => $q->id,
        'score' => rand(60, 100)
    ]);
}

// Create restart log
WorkoutRestartLog::create([
    'workout_id' => $workout->id,
    'used_dda' => false,
    'topic_levels' => ['Algorithm' => 3],
    'previous_score' => 75
]);
```

---

## 🐛 Debugging Checklist

### Check Database State
```bash
# Connect to tinker
php artisan tinker

# Check users
>>> User::role('student')->count()

# Check workouts
>>> Workout::where('is_completed', 1)->count()

# Check restart logs
>>> WorkoutRestartLog::count()

# Check topics
>>> Question::whereNotNull('topic')->distinct('topic')->pluck('topic')
```

### Check Routes
```bash
# List all routes
php artisan route:list | grep comparison

# Output should show:
# GET|HEAD   /mentor/topic-comparison
# GET|HEAD   /mentor/topic-comparison/data
# GET|HEAD   /mentor/topic-comparison/session/{sessionId}
```

### Check Logs
```bash
# Real-time log watching
tail -f storage/logs/laravel.log

# Check for errors in:
# - TopicLevelComparisonController
# - Exception messages
```

---

## 📱 Mobile Optimization Notes

The blade template is fully responsive with Bootstrap 5:

### Responsive Breakpoints
- **xs** (< 576px): Single column, full-width
- **sm** (≥ 576px): Single column
- **md** (≥ 768px): Two columns
- **lg** (≥ 992px): Three columns
- **xl** (≥ 1200px): Full layout

### Mobile Testing
```bash
# Chrome DevTools
1. Press F12
2. Click device toolbar icon
3. Select mobile device
4. Test all tabs and interactions
```

### Touch-Friendly Features
- Large buttons (min 44x44px)
- Adequate spacing between elements
- Full-width dropdowns on mobile

---

## 🔒 Security Checklist

- [x] Authorization: `mentor.list` permission required
- [x] CSRF Protection: Automatic via middleware
- [x] Input Validation: Only numeric IDs passed
- [x] SQL Injection: Using Eloquent ORM
- [x] XSS Protection: Blade auto-escaping enabled
- [x] Rate Limiting: Standard Laravel middleware
- [x] Sensitive Data: No sensitive info in JSON responses

---

## ⚡ Performance Optimization Tips

### 1. Cache Results
```php
$data = cache()->remember('topic-comparison-data', 3600, function() {
    return $this->getComparisonData();
});
```

### 2. Paginate Large Datasets
```php
$students = User::role('student')
    ->paginate(50); // Show 50 at a time
```

### 3. Use Indexes
```sql
CREATE INDEX idx_restart_used_dda ON workout_restart_logs(used_dda);
CREATE INDEX idx_quiz_log_score ON workout_quiz_logs(score);
```

### 4. Lazy Load Charts
```javascript
// Only render chart when tab is active
document.getElementById('tab-dda').addEventListener('shown.bs.tab', function() {
    if (!ddaChartRendered) {
        drawAverageChart('dda-avg-chart', data, ...);
        ddaChartRendered = true;
    }
});
```

---

## 📚 Related Files Reference

| File | Purpose | Status |
|------|---------|--------|
| `app/Models/User.php` | User model | Existing |
| `app/Models/Workout.php` | Workout model | Existing |
| `app/Models/WorkoutRestartLog.php` | Restart log model | Existing |
| `app/Models/WorkoutQuizLog.php` | Quiz log model | Existing |
| `app/Models/Question.php` | Question model | Existing |
| `app/Http/Controllers/TopicLevelComparisonController.php` | New controller | ✅ Created |
| `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php` | New view | ✅ Created |
| `routes/web.php` | Routes | ✅ Updated |

---

## 🎓 Learning Resources

### Understanding the Flow
1. Read `TOPIC_COMPARISON_IMPLEMENTATION.md` for full details
2. Review controller methods one by one
3. Trace data flow through blade template
4. Run tests to verify functionality

### Modifying Features
1. Identify which file to modify
2. Locate the relevant method/section
3. Test changes thoroughly
4. Update documentation

### Extending Features
1. Add new controller method
2. Create new API route
3. Add new blade template section
4. Add corresponding JavaScript
5. Update documentation

---

## 🚀 Deployment Checklist

Before going live:

```bash
# 1. Backup database
php artisan backup:run --only-db

# 2. Clear all caches
php artisan cache:clear
php artisan route:cache
php artisan view:cache

# 3. Run migrations (if needed)
php artisan migrate

# 4. Test in staging
npm run dev  # or production build

# 5. Verify permissions
# Check that 'mentor.list' permission exists in database

# 6. Final smoke tests
# - Access /mentor/topic-comparison
# - Switch all tabs
# - Check console for errors
# - Verify data loads correctly

# 7. Deploy to production
git push production main
# or your deployment process
```

---

**Version**: 1.0
**Last Updated**: April 22, 2026
**Status**: Production Ready

For comprehensive documentation, see: `TOPIC_COMPARISON_IMPLEMENTATION.md`
