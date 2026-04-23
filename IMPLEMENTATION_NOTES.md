# Topic Detection & First-Attempt Non-DDA Implementation

## Overview
This implementation ensures that:
1. **First attempts always start with NON-DDA** (no adaptive difficulty)
2. **Topics are automatically detected** from quiz configuration
3. **Topic levels are tracked** for DDA analytics
4. **Subsequent attempts use DDA** with topic-aware recommendations

## Changes Made

### 1. WorkoutController.php (app/Http/Controllers/learn/WorkoutController.php)

Modified `completedAndNext()` method (lines 67-152) to:

#### 1a. First-Attempt Detection (Line 83)
```php
$isFirstAttempt = $workout->RestartLogs()->count() === 0;
```
- Checks if this is the first attempt by counting existing restart logs
- Returns true if no previous attempts exist

#### 1b. NON-DDA Override for First Attempt (Lines 86-89)
```php
$usedDDA = $workout->used_dda ?? true;
if ($isFirstAttempt) {
    $usedDDA = false;  // First attempt ALWAYS non-DDA
}
```
- Forces first attempt to use NON-DDA mode
- Ensures consistent difficulty progression

#### 1c. Topic Extraction (Lines 93-104)
```php
$topicLevels = [];
try {
    if ($workout->Sessionable && method_exists($workout->Sessionable, 'Model')) {
        $quizModel = $workout->Sessionable->Model;
        if ($quizModel && method_exists($quizModel, 'getTopicsArray')) {
            $topics = $quizModel->getTopicsArray();
            foreach ($topics as $topic) {
                $topicLevels[$topic] = 0;  // Initialize all topics at level 0
            }
        }
    }
} catch (\Throwable $e) { }
```
- Extracts topics from quiz using `getTopicsArray()` method
- Initializes each topic at level 0 for new attempts
- Safely handles errors with try-catch

#### 1d. RestartLog Creation with Topic Levels (Lines 123-131)
```php
WorkoutRestartLog::create([
    'workout_id' => $workout->id,
    'user_id' => Auth::id(),
    'previous_score' => $score,
    'dda_difficulty' => $ddaDifficulty,
    'non_dda_difficulty' => $nonDdaDifficulty,
    'topic_levels' => !empty($topicLevels) ? json_encode($topicLevels) : null,
    'payload' => $sessionLogs,
    'used_dda' => (bool)$usedDDA,
]);
```
- Stores complete attempt history with topic levels
- Encodes `$topicLevels` as JSON for storage

#### 1e. Reset for Next Attempt (Lines 135-138)
```php
$workout->update([
    'used_dda' => 1,  // Next attempts use DDA
    'current_dda_difficulty' => null,
]);
```
- Sets flag to enable DDA for subsequent attempts

### 2. Database Migration

File: `2026_04_20_230402_add_topic_levels_and_used_dda_to_workout_restart_logs_table.php`

Adds two columns to `workout_restart_logs` table:
- `topic_levels` (JSON, nullable): Stores topic-level mapping
- `used_dda` (boolean, default=true): Tracks if DDA was used

### 3. WorkoutRestartLog Model

File: `app/Models/WorkoutRestartLog.php`

Updated `$casts` array:
```php
protected $casts = [
    'payload' => 'array',
    'topic_levels' => 'array',  // Added
];
```
- Automatically converts JSON to array on retrieval
- Automatically converts array to JSON on storage

### 4. Review Page Integration

File: `resources/views/contents/learn/quiz/review.blade.php`

Already has `loadTopicLevelsAnalysis()` function (line 307) that:
- Aggregates topic levels from all restart logs
- Displays topic progress with color-coded bars
- Shows "No topic level data available yet" when empty

## Data Flow

```
1. User completes quiz
   ?
2. completedAndNext() called
   ?
3. Check: Are there any restart logs?
   +- NO (First attempt)
   ¦  +- Set used_dda = false
   ¦
   +- YES (Subsequent attempt)
      +- Use DDA mode
   ?
4. Extract topics from quiz->getTopicsArray()
   ?
5. Initialize topicLevels with all topics at level 0
   ?
6. Create RestartLog with:
   - topic_levels (JSON)
   - used_dda (boolean)
   - previous_score
   - dda_difficulty
   - non_dda_difficulty
   ?
7. Reset workout:
   - used_dda = 1 (enable for next attempt)
   - current_dda_difficulty = null
   ?
8. User redirected to review page
   ?
9. loadTopicLevelsAnalysis() aggregates and displays data
```

## Topic Levels in RestartLog

Example `topic_levels` JSON:
```json
{
  "Arrays": 0,
  "Loops": 0,
  "Functions": 0,
  "Recursion": 0
}
```

After subsequent attempts (populated by Python DDA service):
```json
{
  "Arrays": 2,
  "Loops": 1,
  "Functions": 3,
  "Recursion": 0
}
```

## Key Features

? **First Attempt Non-DDA**: Ensures consistent baseline assessment
? **Automatic Topic Detection**: Extracts from quiz configuration
? **Topic-Level Tracking**: Records progress for analytics
? **DDA Activation**: Enabled for subsequent attempts
? **Review Integration**: Displays topic progress on review page
? **Backward Compatible**: Uses try-catch for safe error handling

## Testing Checklist

- [x] WorkoutController updated with first-attempt detection
- [x] Topic extraction logic implemented
- [x] RestartLog creation includes topic_levels
- [x] Migration created and executed
- [x] Model casts updated
- [x] Review page ready to display data

## Next Steps for Testing

1. Complete a quiz for the first time
   - Verify `used_dda = false` in database
   - Verify `topic_levels` populated with quiz topics
   - Verify review page shows topics

2. Complete the quiz a second time
   - Verify `used_dda = true` (DDA enabled)
   - Verify restart logs accumulate

3. Monitor topic_levels updates
   - Backend Python service should update levels
   - Review page should show progression
