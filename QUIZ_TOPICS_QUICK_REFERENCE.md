# QUIZ TOPIC FEATURE - QUICK REFERENCE

## Implementation Complete ✓

Fitur memungkinkan:
- Input multiple topics saat membuat/edit quiz
- Display topics di mycourses show page sebagai badges
- Foundation untuk filter questions berdasarkan topics

## Database
- Table: quiz_topics
- Fields: id, quiz_id (FK), topic, timestamps
- Constraints: unique(quiz_id, topic), CASCADE delete

## Models

### Quiz Model
```php
$quiz->topics()                    // Get all topics
$quiz->getTopicsArray()           // Get array of topic names
$quiz->getQuestionsFilteredByTopics() // Get questions by topics
```

### QuizTopic Model
- Relationship: belongsTo(Quiz::class)

## Form Usage
- Select2 dropdown dengan multi-select capability
- Pre-population di edit mode
- Bootstrap 5 theme
- Located: resources/views/contents/admin/quiz/form.blade.php

## Controller
- store(): Extract topics, create quiz, insert topics
- update(): Update quiz, delete old topics, insert new ones
- Located: app/Http/Controllers/QuizController.php

## Frontend Display
- Topics ditampilkan sebagai info badges
- Bookmark icon untuk setiap topic
- Located: resources/views/components/box/session-activity-item.blade.php
- Visible di: learn/mycourses/{participant}/show

## API/Usage Examples

### Create Quiz dengan Topics
```php
POST /panel/quiz
{
    "title": "Quiz 1",
    "duration": 30,
    "min_pass_score": 80,
    "topics": ["Database", "SQL", "Normalization"]
}
```

### Get Topics untuk Quiz
```php
$quiz = Quiz::find(1);
$topics = $quiz->getTopicsArray();
// Output: ["Database", "SQL", "Normalization"]
```

### Get Questions by Topics (Future Use)
```php
$quiz = Quiz::find(1);
$questions = $quiz->getQuestionsFilteredByTopics();
```

## Testing Checklist
- [ ] Create quiz dengan 1+ topics
- [ ] Edit quiz topics
- [ ] Verify topics di database
- [ ] Check topics display di mycourses show
- [ ] Test dengan empty topics (should allow all questions)

## File Locations
1. Migration: database/migrations/2026_04_20_create_quiz_topics_table.php
2. Models: app/Models/Quiz.php, app/Models/QuizTopic.php
3. Form: resources/views/contents/admin/quiz/form.blade.php
4. Controller: app/Http/Controllers/QuizController.php
5. Component: resources/views/components/box/session-activity-item.blade.php

## Status
- Implementation: ✅ COMPLETE
- Database: ✅ MIGRATED
- Backend: ✅ READY
- Frontend: ✅ READY
- Testing: ⏳ PENDING

Generated: 2026-04-20
