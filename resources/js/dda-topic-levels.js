/**
 * Topic Levels & DDA Analysis Helper
 * Digunakan dalam review.blade.php untuk menampilkan topic levels dan pie chart topik
 */

function loadTopicLevels() {
    const latestTopicLevels = {};

    // Collect all topic levels dari semua restart logs
    if (typeof allRestartLogs !== 'undefined') {
        allRestartLogs.forEach(log => {
            if(log.topic_levels && typeof log.topic_levels === 'object') {
                Object.assign(latestTopicLevels, log.topic_levels);
            }
        });
    }

    const container = document.getElementById('topic-levels-container');
    if (!container) return;

    if(Object.keys(latestTopicLevels).length === 0) {
        container.innerHTML = '<div class="text-muted"><i class="fas fa-info-circle me-1"></i> No topic data available.</div>';
        return;
    }

    let html = '<div class="row g-3">';

    Object.entries(latestTopicLevels).sort().forEach(([topic, level]) => {
        const color = level >= 4 ? 'success' : level >= 3 ? 'info' : level >= 2 ? 'warning' : 'secondary';
        const width = (level / 5) * 100;

        html += `
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>${topic}</strong>
                            <span class="badge bg-${color}">Level ${level}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-${color}" role="progressbar"
                                 style="width: ${width}%"
                                 aria-valuenow="${level}" aria-valuemin="0" aria-valuemax="5">
                                ${level}/5
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Progress: ${Math.round(width)}%</small>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadTopicLevels();
});
