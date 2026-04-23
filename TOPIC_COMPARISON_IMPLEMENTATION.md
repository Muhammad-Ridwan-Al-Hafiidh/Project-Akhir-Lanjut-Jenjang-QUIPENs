# Topic Level Comparison Feature - Implementation Guide

## Overview
This document provides complete implementation details for the **Topic Level Comparison Feature** with DDA vs Non-DDA comparison functionality.

## Files Created

### 1. Controller: `TopicLevelComparisonController.php`
**Location:** `app/Http/Controllers/TopicLevelComparisonController.php`

#### Key Methods:

##### `getComparisonData()`
- **Purpose**: API endpoint returning comparison data across all students
- **Authorization**: Requires `mentor.list` permission
- **Returns JSON with**:
  - `students`: Student ID/Name mapping
  - `topics`: Array of all unique topics
  - `dda_data`: Student levels in DDA mode per topic
  - `nondda_data`: Student levels in Non-DDA mode per topic
  - `overall_data`: Combined student levels
  - `stats`: Average statistics for all three modes
- **Response Format**:
  ```json
  {
    "success": true,
    "students": {1: "John Doe", 2: "Jane Smith"},
    "topics": ["T1", "T2", "T3"],
    "dda_data": [
      {"student_id": 1, "student_name": "John Doe", "levels": {"T1": 4, "T2": 3}}
    ],
    "nondda_data": [
      {"student_id": 1, "student_name": "John Doe", "levels": {"T1": 3, "T2": 2}}
    ],
    "overall_data": [...],
    "stats": {
      "dda_avg": {"T1": 3.5, "T2": 2.8},
      "nondda_avg": {"T1": 2.9, "T2": 2.1},
      "overall_avg": {...},
      "dda_count": 15,
      "nondda_count": 18,
      "total_students": 20
    }
  }
  ```

##### `getSessionComparison($sessionId)`
- **Purpose**: Get comparison data for a specific session
- **Authorization**: Requires `mentor.list` permission
- **Returns**: Similar to `getComparisonData()` but filtered to a session

##### `index()`
- **Purpose**: Display the comparison view
- **Returns**: Blade template view

#### Private Helper Methods:

##### `aggregateTopicLevels(&$topicLevels, Workout $workout)`
- **Purpose**: Aggregate topic levels from a workout's quiz logs
- **Params**: 
  - `$topicLevels`: Reference array to update with max levels
  - `$workout`: Workout model instance
- **Logic**: Updates the array with the maximum topic level achieved

##### `calculateTopicStats($quizLogs)`
- **Purpose**: Convert quiz performance to topic levels
- **Formula**: 
  ```
  Accuracy = (sum(scores) / (count * 100)) * 100
  Level Mapping:
    80%+ → Level 4
    60-79% → Level 3
    40-59% → Level 2
    1-39% → Level 1
    0% → Level 0
  ```
- **Returns**: Array of topic => level mappings

##### `calculateAverageStats(array $data, array $topics)`
- **Purpose**: Calculate average statistics across students
- **Returns**: Average levels per topic rounded to 2 decimals

---

### 2. View: `topic-levels-comparison.blade.php`
**Location:** `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php`

#### Structure:
- **Header Card**: Feature title and description
- **Legend Card**: Color coding explanation (Level 0-4)
- **Three Tabs**:
  1. **Non-DDA Tab**: 
     - Statistics summary
     - Heatmap showing all students' levels
     - Bar chart of average levels by topic
  2. **DDA Tab**: 
     - Statistics summary
     - Heatmap showing all students' levels
     - Bar chart of average levels by topic
  3. **Comparison Tab**: 
     - Summary cards for both modes
     - Side-by-side bar chart comparing DDA vs Non-DDA

#### Color Scheme:
- **Level 4**: Green (`bg-success`)
- **Level 3**: Blue (`bg-info`)
- **Level 2**: Orange (`bg-warning`)
- **Level 1**: Gray (`bg-secondary`)
- **Level 0**: Light Gray with border (`bg-light text-dark border`)

#### JavaScript Functions:

##### `loadComparisonData()`
- Fetches data from API endpoint
- Shows loading state during fetch
- Returns parsed JSON or null on error

##### `loadNonDDAAnalysis(data)`
- Renders Non-DDA statistics
- Generates heatmap table
- Creates bar chart for average levels

##### `loadDDAAnalysis(data)`
- Renders DDA statistics
- Generates heatmap table
- Creates bar chart for average levels

##### `loadComparisonAnalysis(data)`
- Renders comparison summary
- Creates side-by-side comparison chart

##### `generateHeatmap(studentData, topics)`
- **Returns**: HTML table with color-coded levels
- **Format**: Rows = students, Columns = topics
- **Styling**: Level-based color badges

##### `drawAverageChart(canvasId, avgData, topics, title, color)`
- **Purpose**: Draw bar chart sorted by level (highest first)
- **Uses**: Chart.js library
- **Features**: Responsive, animated bars

##### `drawComparisonChart(canvasId, ddaAvg, nonddaAvg, topics)`
- **Purpose**: Side-by-side comparison chart
- **Uses**: Chart.js grouped bar chart
- **Features**: Legend, colors for each mode

##### Utility Functions:
- `getLevelColor(level)`: Returns Bootstrap color class for level
- `showError(message)`: Display error alert
- `showLoading(show)`: Show/hide loading spinner

---

## Routes

### Web Routes (in `routes/web.php`)

```php
Route::prefix('mentor')->middleware(['verified'])->group(function () {
    // ... existing routes ...
    
    // Topic Level Comparison (DDA vs Non-DDA)
    Route::get('/topic-comparison', [TopicLevelComparisonController::class, 'index'])
        ->name('topicComparison');
    
    Route::get('/topic-comparison/data', [TopicLevelComparisonController::class, 'getComparisonData'])
        ->name('api.topic-comparison');
    
    Route::get('/topic-comparison/session/{sessionId}', [TopicLevelComparisonController::class, 'getSessionComparison'])
        ->name('sessionComparison');
});
```

### Routes Summary:
- **`/mentor/topic-comparison`**: Main view (GET)
- **`/mentor/topic-comparison/data`**: API data endpoint (GET)
- **`/mentor/topic-comparison/session/{sessionId}`**: Session-specific comparison (GET)

---

## Data Flow

### 1. User Access
```
Mentor clicks "Topic Comparison" link
↓
GET /mentor/topic-comparison
↓
TopicLevelComparisonController::index()
↓
Renders topic-levels-comparison.blade.php
```

### 2. Data Loading
```
View JavaScript calls loadComparisonData()
↓
fetch() to GET /mentor/topic-comparison/data
↓
TopicLevelComparisonController::getComparisonData()
↓
Returns JSON with all student/topic/level data
↓
JavaScript parses and stores data
```

### 3. Data Aggregation
```
For each Student:
  ├─ For each Workout:
  │  ├─ Get RestartLogs
  │  │  ├─ If used_dda == true → Aggregate to DDA data
  │  │  └─ If used_dda == false → Aggregate to Non-DDA data
  │  │
  │  └─ Get WorkoutQuizLog entries
  │     ├─ Group by Question.topic
  │     ├─ Calculate accuracy = sum(scores) / (count * 100) * 100
  │     └─ Convert to level (0-4)
  │
  └─ For each Topic:
     ├─ Get max level from all attempts (DDA mode)
     ├─ Get max level from all attempts (Non-DDA mode)
     └─ Get max level from all current quiz logs
```

---

## Database Relationships

### Models Used:
1. **User** - Students with `role('student')`
2. **Workout** - Student workout records
3. **WorkoutRestartLog** - Tracks each restart with:
   - `used_dda` (boolean): Whether DDA was used
   - `topic_levels` (json): Topic levels from this restart
4. **WorkoutQuizLog** - Individual quiz answers
5. **Question** - Quiz questions with `topic` field

### Key Relationships:
```
User
├─ Workouts (hasMany)
│  ├─ RestartLogs (hasMany - WorkoutRestartLog)
│  └─ WorkOutQuiz (hasMany - WorkoutQuizLog)
│     └─ Question (belongsTo)
│        └─ topic (string field)
```

---

## Query Optimization

### Eager Loading:
The controller uses eager loading to minimize queries:
```php
User::role('student')
    ->with([
        'Workouts' => function ($query) {
            $query->with([
                'RestartLogs',
                'WorkOutQuiz.Question'
            ]);
        }
    ])
    ->get();
```

### Performance Notes:
- Single query per relation type
- Minimal N+1 query problems
- Suitable for up to ~1000 students
- For larger datasets, consider pagination or filtering

---

## Usage Examples

### Access the Feature:
1. Login as a Mentor
2. Navigate to `https://yourapp.com/mentor/topic-comparison`
3. Wait for data to load
4. Click tabs to switch between views

### API Direct Access:
```bash
# Get all comparison data
curl -X GET https://yourapp.com/mentor/topic-comparison/data \
  -H "Authorization: Bearer {token}"

# Get session-specific data
curl -X GET https://yourapp.com/mentor/topic-comparison/session/1 \
  -H "Authorization: Bearer {token}"
```

### JavaScript Access:
```javascript
// Get data programmatically
const data = await loadComparisonData();

// Access specific data
console.log(data.dda_data);  // DDA mode students
console.log(data.nondda_data); // Non-DDA mode students
console.log(data.stats); // Statistics
```

---

## Error Handling

### Authorization:
- Returns 403 Forbidden if user lacks `mentor.list` permission

### Data Loading:
- Catches exceptions and returns 500 with error message
- Frontend shows user-friendly error alerts

### Missing Data:
- Returns empty arrays instead of errors
- Frontend shows info message for empty tabs

---

## Testing Checklist

- [ ] Verify mentor can access `/mentor/topic-comparison`
- [ ] Verify non-mentor gets 403 error
- [ ] Verify all students display correctly
- [ ] Verify all topics aggregate correctly
- [ ] Verify level calculations are accurate
- [ ] Verify DDA and Non-DDA data are separated
- [ ] Verify statistics calculate correctly
- [ ] Verify charts render properly
- [ ] Verify tabs switch smoothly
- [ ] Verify heatmap colors are correct
- [ ] Test with 0 students (should show empty state)
- [ ] Test with various level distributions
- [ ] Verify responsive design on mobile
- [ ] Verify no JavaScript errors in console

---

## Customization Options

### Color Scheme:
Edit in `topic-levels-comparison.blade.php` `@push('styles')` section:
```css
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
```

### Chart Types:
In JavaScript, change `type: 'bar'` to:
- `'line'` - Line chart
- `'pie'` - Pie chart
- `'doughnut'` - Donut chart
- `'radar'` - Radar chart

### Level Thresholds:
Edit in `TopicLevelComparisonController::calculateTopicStats()`:
```php
if ($accuracy >= 80) { // Change 80 to desired threshold
    $topicLevels[$topic] = 4;
}
```

---

## Troubleshooting

### No Data Appearing:
1. Check if users have role 'student'
2. Check if workouts are marked as `is_completed = 1`
3. Check if RestartLogs exist with `used_dda` values
4. Check browser console for JavaScript errors

### Wrong Level Calculations:
1. Verify quiz log `score` field is 0-100 scale
2. Check question `topic` field is populated
3. Verify RestartLog `used_dda` field is set correctly

### Charts Not Rendering:
1. Check Chart.js library loads: `https://cdn.jsdelivr.net/npm/chart.js@3.9.1`
2. Check browser console for errors
3. Verify data structure in Network tab

---

## Future Enhancements

- [ ] Export data to CSV/Excel
- [ ] Filter by date range
- [ ] Filter by specific session
- [ ] Export charts as images
- [ ] Comparison with previous attempts
- [ ] Individual student detailed view
- [ ] Predictive analytics
- [ ] Download PDF report

---

## Files Modified/Created

| File | Type | Purpose |
|------|------|---------|
| `app/Http/Controllers/TopicLevelComparisonController.php` | Created | Main controller for comparison logic |
| `resources/views/contents/learn/quiz/topic-levels-comparison.blade.php` | Created | Frontend view with tabs and charts |
| `routes/web.php` | Modified | Added comparison routes |

---

## Support & Questions

For issues or questions, please refer to:
1. Check this documentation
2. Review code comments in controller
3. Check browser console for errors
4. Verify database has required data

---

**Version**: 1.0
**Last Updated**: April 22, 2026
**Status**: Production Ready
