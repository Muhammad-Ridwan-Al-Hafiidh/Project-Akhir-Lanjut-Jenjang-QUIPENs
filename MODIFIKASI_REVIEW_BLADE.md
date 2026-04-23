# Modifikasi untuk review.blade.php

## 1. Tambahkan Script untuk Loading Topic Levels

Di sebelum closing `</script>` tag (sebelum `createOverlayChart` function), tambahkan:

```javascript
function loadTopicLevels() {
    const latestTopicLevels = {};
    
    // Collect all topic levels dari semua restart logs
    allRestartLogs.forEach(log => {
        if(log.topic_levels && typeof log.topic_levels === ''object'') {
            Object.assign(latestTopicLevels, log.topic_levels);
        }
    });

    const container = document.getElementById(''topic-levels-container'');
    
    if(Object.keys(latestTopicLevels).length === 0) {
        container.innerHTML = ''<div class="text-muted"><i class="fas fa-info-circle me-1"></i> No topic data available.</div>'';
        return;
    }

    let html = ''<div class="row g-3">'';
    
    Object.entries(latestTopicLevels).sort().forEach(([topic, level]) => {
        const color = level >= 4 ? ''success'' : level >= 3 ? ''info'' : level >= 2 ? ''warning'' : ''secondary'';
        const width = (level / 5) * 100;
        
        html += `
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>$` + `{topic}</strong>
                            <span class="badge bg-$` + `{color}">Level $` + `{level}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-$` + `{color}" role="progressbar" 
                                 style="width: $` + `{width}%" 
                                 aria-valuenow="$` + `{level}" aria-valuemin="0" aria-valuemax="5">
                                $` + `{level}/5
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Progress: $` + `{Math.round(width)}%</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += ''</div>'';
    container.innerHTML = html;
}

// Panggil setelah semua data siap
loadTopicLevels();
```

## 2. Tambahkan Card untuk Topic Levels

Sebelum closing `</div>` dari card body (setelah Restart History section), tambahkan:

```blade
{{-- DDA Analysis & Topic Levels Card --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="m-0 text-muted"><i class="fas fa-brain me-2"></i> Topic Levels & Progress</h6>
    </div>
    <div class="card-body">
        <div id="topic-levels-container">
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin me-2"></i> Loading topic levels...
            </div>
        </div>
    </div>
</div>
```

## 3. Update Pie Chart Display untuk Topic

Dalam function loadAnalysis, tambahkan display untuk topic pie chart:

```javascript
// Di dalam loop Object.entries(graphs)
if(k === ''topic_pie'' && v) {
    const col = document.createElement(''div'');
    col.className = ''col-12 col-md-6'';
    const card = document.createElement(''div'');
    card.className = ''card h-100'';
    const header = document.createElement(''div'');
    header.className = ''card-header py-2 bg-light'';
    header.innerHTML = ''<small class="text-muted">TOPIC DISTRIBUTION</small>'';
    const body = document.createElement(''div'');
    body.className = ''card-body text-center p-2'';
    const img = document.createElement(''img'');
    img.src = v;
    img.alt = k;
    img.className = ''img-fluid'';
    body.appendChild(img);
    card.appendChild(header);
    card.appendChild(body);
    col.appendChild(card);
    graphsEl.appendChild(col);
}
```

## 4. Struktur Data yang Diharapkan

Pastikan RestartLog di database/backend mencakup:
- dda_topic (topic yang digunakan dalam mode DDA)
- non_dda_topic (topic dalam mode non-DDA)  
- topic_levels (JSON dict mapping topic -> level)

Contoh:
```json
{
    "created_at": "2026-04-20 10:00:00",
    "dda_difficulty": "medium",
    "dda_topic": "Algebra",
    "non_dda_difficulty": "easy",
    "non_dda_topic": "Geometry",
    "previous_score": 75.5,
    "used_dda": true,
    "topic_levels": {
        "Algebra": 3,
        "Geometry": 2,
        "Calculus": 1
    }
}
```

## 5. Update Database Schema (Laravel Migration)

Jika belum ada, buat migration:

```php
Schema::table(''restart_logs'', function (Blueprint $table) {
    $table->string(''dda_topic'')->nullable()->after(''dda_difficulty'');
    $table->string(''non_dda_topic'')->nullable()->after(''non_dda_difficulty'');
    $table->json(''topic_levels'')->nullable()->after(''used_dda'');
});
```
