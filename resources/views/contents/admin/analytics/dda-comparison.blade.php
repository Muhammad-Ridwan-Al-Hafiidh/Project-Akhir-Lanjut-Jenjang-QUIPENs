@extends('layouts.admin')
@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-balance-scale me-2 text-primary"></i>Analisis Perbedaan DDA vs Non-DDA
            </h1>
            <p class="text-muted small mb-0">Bandingkan rata-rata level topik antara metode DDA dan Non-DDA berdasarkan mahasiswa yang dipilih</p>
        </div>
        <div>
            <span class="badge bg-info text-white p-2" id="selected-student-count">0 mahasiswa dipilih</span>
        </div>
    </div>

    <!-- Filter Topik -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-1"></i> Pilih Topik
            </h6>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-1" id="select-all-topics">Pilih Semua</button>
                <button class="btn btn-sm btn-outline-secondary me-2" id="clear-all-topics">Hapus Pilihan</button>
                <button id="analyze-btn" class="btn btn-primary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i> Analisis
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="topic-list">
                @forelse ($topics as $topic)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                        <div class="form-check">
                            <input class="form-check-input topic-checkbox" type="checkbox" value="{{ $topic }}" id="topic_{{ $loop->index }}">
                            <label class="form-check-label text-truncate" for="topic_{{ $loop->index }}" title="{{ $topic }}">
                                {{ $topic }}
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">Tidak ada topik tersedia.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-2 text-end">
                <small class="text-muted" id="selected-topic-hint">Belum ada topik dipilih</small>
            </div>
        </div>
    </div>

    <!-- Loading & Error -->
    <div id="loading-spinner" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Menganalisis data, mohon tunggu...</p>
    </div>

    <div id="error-msg" class="alert alert-danger" style="display: none;"></div>

    <!-- Results -->
    <div id="results" style="display: none;">

        <!-- Filter Mahasiswa -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-check me-1"></i> Filter Mahasiswa
                </h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="student-search" class="form-control" placeholder="Cari mahasiswa...">
                    <button class="btn btn-sm btn-outline-secondary" id="select-all-students">Pilih Semua</button>
                    <button class="btn btn-sm btn-outline-secondary" id="clear-all-students">Hapus Pilihan</button>
                    <span class="input-group-text bg-white fw-bold" id="student-filter-count">0</span>
                </div>
                <div id="student-list" class="d-flex flex-wrap gap-2" style="max-height: 180px; overflow-y: auto; padding: 8px 4px; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                    <!-- akan diisi oleh JS -->
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-1"></i> Perbandingan Rata-rata Level Topik
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 350px;">
                    <canvas id="comparisonChart"></canvas>
                </div>
                <p class="text-muted small text-center mt-2">* Nilai level berkisar 0–5, semakin tinggi menandakan penguasaan lebih baik</p>
            </div>
        </div>

        <!-- Tabel Perbandingan -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-check-circle me-1"></i> Data DDA
                        </h6>
                        <span class="badge bg-success" id="dda-count">0 siswa</span>
                    </div>
                    <div class="card-body" id="ddaTable">
                        <p class="text-muted text-center">Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-times-circle me-1"></i> Data Non-DDA
                        </h6>
                        <span class="badge bg-danger" id="nondda-count">0 siswa</span>
                    </div>
                    <div class="card-body" id="nondDaTable">
                        <p class="text-muted text-center">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DOM refs ---
        const topicCheckboxes = document.querySelectorAll('.topic-checkbox');
        const selectAllTopics = document.getElementById('select-all-topics');
        const clearAllTopics = document.getElementById('clear-all-topics');
        const analyzeBtn = document.getElementById('analyze-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const errorDiv = document.getElementById('error-msg');
        const resultsDiv = document.getElementById('results');
        const studentList = document.getElementById('student-list');
        const selectedTopicHint = document.getElementById('selected-topic-hint');
        const selectedStudentCount = document.getElementById('selected-student-count');
        const studentFilterCount = document.getElementById('student-filter-count');

        // --- variables ---
        let allData = null;
        let comparisonChart = null;
        let excludedStudents = new Set();

        // --- helper update topic selection info ---
        function updateTopicInfo() {
            const checked = document.querySelectorAll('.topic-checkbox:checked');
            const count = checked.length;
            if (count === 0) {
                selectedTopicHint.textContent = 'Belum ada topik dipilih';
                selectedTopicHint.className = 'text-muted';
            } else {
                const names = Array.from(checked).map(cb => cb.nextElementSibling.textContent.trim());
                selectedTopicHint.textContent = 'Terpilih: ' + names.join(', ');
                selectedTopicHint.className = 'text-success';
            }
        }
        topicCheckboxes.forEach(cb => cb.addEventListener('change', updateTopicInfo));
        selectAllTopics.addEventListener('click', function() {
            topicCheckboxes.forEach(cb => cb.checked = true);
            updateTopicInfo();
        });
        clearAllTopics.addEventListener('click', function() {
            topicCheckboxes.forEach(cb => cb.checked = false);
            updateTopicInfo();
        });
        updateTopicInfo();

        // --- search student ---
        document.getElementById('student-search').addEventListener('input', renderStudentList);

        // --- analyze button ---
        analyzeBtn.addEventListener('click', analyze);

        async function analyze() {
            const topicIds = Array.from(document.querySelectorAll('.topic-checkbox:checked')).map(cb => cb.value);
            if (topicIds.length === 0) {
                alert('Pilih minimal satu topik untuk dianalisis.');
                return;
            }

            // Show loading, hide results & error
            loadingSpinner.style.display = 'block';
            resultsDiv.style.display = 'none';
            errorDiv.style.display = 'none';
            analyzeBtn.disabled = true;

            try {
                const response = await fetch('{{ route('api.analytics.dda-comparison') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topic_ids: topicIds })
                });

                const data = await response.json();

                if (!data.success) {
                    errorDiv.textContent = data.error || 'Gagal memuat data';
                    errorDiv.style.display = 'block';
                    return;
                }

                allData = data;
                excludedStudents = new Set();
                renderAll();
                resultsDiv.style.display = 'block';
            } catch (e) {
                errorDiv.textContent = 'Error: ' + e.message;
                errorDiv.style.display = 'block';
            } finally {
                loadingSpinner.style.display = 'none';
                analyzeBtn.disabled = false;
            }
        }

        // --- render data ---
        function renderAll() {
            if (!allData || !allData.students || allData.students.length === 0) {
                studentList.innerHTML = '<p class="text-muted text-center w-100">Tidak ada data mahasiswa.</p>';
                return;
            }

            // Reset search
            const searchInput = document.getElementById('student-search');
            if (searchInput) searchInput.value = '';

            renderStudentList();

            // Add select/deselect all for students
            const selectAllStudents = document.getElementById('select-all-students');
            const clearAllStudents = document.getElementById('clear-all-students');
            selectAllStudents.onclick = function() {
                excludedStudents = new Set();
                document.querySelectorAll('.student-cb').forEach(cb => cb.checked = true);
                renderStudentList();
                applyFilter();
            };
            clearAllStudents.onclick = function() {
                allData.students.forEach(s => excludedStudents.add(s.id));
                document.querySelectorAll('.student-cb').forEach(cb => cb.checked = false);
                renderStudentList();
                applyFilter();
                const search = document.getElementById('student-search');
                if (search) search.value = '';
            };

            updateStudentCount();
            applyFilter();
        }

        // --- render student list with search filter & removable badges ---
        function renderStudentList() {
            if (!allData || !allData.students) return;
            const q = (document.getElementById('student-search').value || '').toLowerCase().trim();

            studentList.innerHTML = '';

            // Show selected badges on top (only when not searching)
            if (!q) {
                const selected = allData.students.filter(s => !excludedStudents.has(s.id));
                if (selected.length > 0) {
                    const badgeContainer = document.createElement('div');
                    badgeContainer.className = 'mb-2 d-flex flex-wrap gap-1';
                    selected.forEach(s => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-1';
                        badge.style.cursor = 'pointer';
                        badge.textContent = s.name;
                        const x = document.createElement('i');
                        x.className = 'fas fa-times ms-1';
                        x.style.pointerEvents = 'none';
                        badge.appendChild(x);
                        badge.onclick = function() { excludedStudents.add(s.id); applyFilter(); renderStudentList(); };
                        badgeContainer.appendChild(badge);
                    });
                    studentList.appendChild(badgeContainer);
                } else {
                    studentList.innerHTML = '<p class="text-muted text-center w-100">Belum ada mahasiswa dipilih. Ketik nama untuk mencari dan memilih.</p>';
                    updateStudentCount();
                    return;
                }
            }

            // Show search results (checkboxes)
            if (q) {
                const shown = [];
                allData.students.forEach(s => {
                    if (!s.name.toLowerCase().includes(q)) return;
                    shown.push(s);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'form-check form-check-inline mb-0';
                    const cb = document.createElement('input');
                    cb.className = 'form-check-input student-cb';
                    cb.type = 'checkbox';
                    cb.value = s.id;
                    cb.checked = !excludedStudents.has(s.id);
                    cb.dataset.name = s.name;
                    cb.addEventListener('change', function() {
                        if (this.checked) excludedStudents.delete(parseInt(this.value));
                        else excludedStudents.add(parseInt(this.value));
                        applyFilter();
                        renderStudentList();
                    });
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.textContent = s.name;
                    wrapper.appendChild(cb);
                    wrapper.appendChild(label);
                    studentList.appendChild(wrapper);
                });
                if (shown.length === 0) {
                    studentList.innerHTML += '<p class="text-muted text-center w-100 mt-2">Tidak ada mahasiswa sesuai pencarian.</p>';
                }
            }

            updateStudentCount();
        }

        function updateStudentCount() {
            const total = allData ? allData.students.length : 0;
            const count = total - excludedStudents.size;
            selectedStudentCount.textContent = count + ' mahasiswa dipilih';
            studentFilterCount.textContent = count + '/' + total;
        }

        function applyFilter() {
            let ids;
            const domCheckboxes = document.querySelectorAll('.student-cb');
            if (domCheckboxes.length > 0) {
                // Search mode: read from DOM checkboxes, then apply exclusions
                ids = new Set(Array.from(document.querySelectorAll('.student-cb:checked')).map(cb => parseInt(cb.value)));
                excludedStudents.forEach(id => ids.delete(id));
            } else {
                // Badge mode: use excludedStudents directly
                ids = new Set(allData.students.map(s => s.id));
                excludedStudents.forEach(id => ids.delete(id));
            }

            const dda = allData.dda_data.filter(s => ids.has(s.id));
            const nondda = allData.nondda_data.filter(s => ids.has(s.id));
            const topics = allData.topics || [];

            drawChart(topics, dda, nondda);
            renderTable('ddaTable', dda, 'dda-count');
            renderTable('nondDaTable', nondda, 'nondda-count');
            updateStudentCount();
        }

        function drawChart(topics, ddaData, nonddaData) {
            const canvas = document.getElementById('comparisonChart');
            if (!canvas) return;
            if (comparisonChart) comparisonChart.destroy();
            if (!topics.length) {
                comparisonChart = new Chart(canvas, {
                    type: 'bar',
                    data: { labels: ['Tidak ada topik'], datasets: [{ label: 'DDA', data: [0], backgroundColor: 'rgba(0,0,0,0.1)' }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
                return;
            }

            const ddaAvg = topics.map(t => {
                const vals = ddaData.map(s => s.levels[t]).filter(v => v != null);
                return vals.length ? +(vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2) : 0;
            });
            const nonddaAvg = topics.map(t => {
                const vals = nonddaData.map(s => s.levels[t]).filter(v => v != null);
                return vals.length ? +(vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2) : 0;
            });

            comparisonChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: topics,
                    datasets: [
                        {
                            label: 'DDA',
                            data: ddaAvg,
                            backgroundColor: 'rgba(40,167,69,0.7)',
                            borderColor: '#28a745',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Non-DDA',
                            data: nonddaAvg,
                            backgroundColor: 'rgba(220,53,69,0.7)',
                            borderColor: '#dc3545',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function renderTable(elementId, data, badgeId) {
            const el = document.getElementById(elementId);
            const badge = document.getElementById(badgeId);
            if (!el) return;

            if (badge) badge.textContent = data.length + ' siswa';

            if (data.length === 0) {
                el.innerHTML = '<p class="text-muted text-center my-3">Tidak ada data siswa untuk kategori ini.</p>';
                return;
            }

            function levelBadge(topic, level) {
                const lv = parseInt(level);
                let label = '', cls = '';
                if (lv >= 3) { label = 'Kuat'; cls = 'bg-success'; }
                else if (lv >= 1) { label = 'Sedang'; cls = 'bg-warning text-dark'; }
                else { label = 'Rendah'; cls = 'bg-danger'; }
                return `<span class="badge ${cls} text-white me-1">${topic}: ${label} (level ${lv})</span>`;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Level Topik</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            data.forEach(s => {
                const levels = Object.entries(s.levels || {})
                    .map(([t, l]) => levelBadge(t, l))
                    .join(' ');
                html += `<tr><td><strong>${s.name}</strong></td><td>${levels || '<span class="text-muted">-</span>'}</td></tr>`;
            });
            html += '</tbody></table></div>';
            el.innerHTML = html;
        }
    });
</script>

<style>
    /* Additional custom styles */
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    #student-list .form-check {
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .card-header .btn-sm {
        margin-left: 4px;
    }
    @media (max-width: 576px) {
        .chart-container {
            height: 250px;
        }
        .card-header .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endsection
