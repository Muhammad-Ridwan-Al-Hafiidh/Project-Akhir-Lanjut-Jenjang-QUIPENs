@extends('layouts.admin')
@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-graduate me-2 text-primary"></i>Rekap Data Mahasiswa untuk SPK
            </h1>
            <p class="text-muted small mb-0">Analisis performa dan rekomendasi intervensi berdasarkan topik yang dipilih</p>
        </div>
        <div>
            <span class="badge bg-info text-white p-2" id="selected-count">0 topik dipilih</span>
            <button id="export-csv-btn" class="btn btn-success btn-sm ms-2" style="display: none;">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
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
                    <i class="fas fa-sync me-1"></i> Generate Rekomendasi
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
                <small class="text-muted" id="selected-hint">Belum ada topik dipilih</small>
            </div>
        </div>
    </div>

    <!-- Loading & Error -->
    <div id="loading-spinner" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Sedang memproses rekomendasi, mohon tunggu...</p>
    </div>

    <div id="error-msg" class="alert alert-danger" style="display: none;"></div>

    <!-- Filter Mahasiswa -->
    <div id="student-section" class="card shadow mb-4" style="display: none;">
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
            <div id="student-list" class="d-flex flex-wrap gap-2" style="max-height: 150px; overflow-y: auto; padding: 8px 4px; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                <!-- diisi JS -->
            </div>
        </div>
    </div>

    <!-- Hasil Rekomendasi -->
    <div id="results" style="display: none;">

        <!-- Summary Cards -->
        <div class="row g-3 mb-4" id="summary-cards">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-body">
                        <div class="text-muted small">Total Mahasiswa</div>
                        <div class="h5 mb-0 fw-bold" id="total-students">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-left-success">
                    <div class="card-body">
                        <div class="text-muted small">Rata-rata Skor DDA</div>
                        <div class="h5 mb-0 fw-bold" id="avg-dda">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-left-danger">
                    <div class="card-body">
                        <div class="text-muted small">Rata-rata Skor Non-DDA</div>
                        <div class="h5 mb-0 fw-bold" id="avg-non-dda">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-body">
                        <div class="text-muted small">Rata-rata Peningkatan DDA</div>
                        <div class="h5 mb-0 fw-bold" id="avg-improvement">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekomendasi -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-1"></i> Rekomendasi Intervensi Mahasiswa
                </h6>
                <span class="badge bg-secondary" id="rec-count">0 data</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle" id="recommendations-table">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th class="text-center">Rata-rata Nilai Ujian DDA</th>
                                <th class="text-center">Rata-rata Nilai Ujian Non-DDA</th>
                                <th class="text-center">Peningkatan DDA</th>
                                <th>Topik Lemah</th>
                                <th>Topik Kuat</th>
                                <th class="text-center">Level Intervensi</th>
                                <th>Rekomendasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="recommendations-body"></tbody>
                    </table>
                </div>
                <div id="no-rec-data" class="text-center text-muted py-3" style="display: none;">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada data rekomendasi.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Rapor -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Rapor Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="report-content">
                <!-- diisi JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="download-report-btn">
                    <i class="fas fa-download me-1"></i> Download Rapor (PDF)
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- Modal untuk Rapor Mata Kuliah -->
<div class="modal fade" id="courseReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-graduation-cap me-2"></i>Rapor Mata Kuliah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="course-report-content">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    .border-left-danger { border-left: 4px solid #e74a3b; }
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .topic-checkbox:checked + label {
        font-weight: 600;
        color: #0d6efd;
    }
    .form-check-label {
        cursor: pointer;
        font-size: 0.9rem;
    }
    #student-list .form-check {
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    @media (max-width: 576px) {
        .card-header .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .table-sm {
            font-size: 0.75rem;
        }
    }
    .badge-intervention {
        min-width: 60px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM refs
        const topicCheckboxes = document.querySelectorAll('.topic-checkbox');
        const selectAllTopics = document.getElementById('select-all-topics');
        const clearAllTopics = document.getElementById('clear-all-topics');
        const analyzeBtn = document.getElementById('analyze-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const errorDiv = document.getElementById('error-msg');
        const resultsDiv = document.getElementById('results');
        const studentSection = document.getElementById('student-section');
        const studentList = document.getElementById('student-list');
        const selectedCount = document.getElementById('selected-count');
        const selectedHint = document.getElementById('selected-hint');
        const exportCsvBtn = document.getElementById('export-csv-btn');

        // state
        let allData = null;
        let excludedStudents = new Set();

        // --- topic selection ---
        function updateTopicInfo() {
            const checked = document.querySelectorAll('.topic-checkbox:checked');
            const count = checked.length;
            selectedCount.textContent = count + ' topik dipilih';
            if (count === 0) {
                selectedHint.textContent = 'Belum ada topik dipilih';
                selectedHint.className = 'text-muted';
            } else {
                const names = Array.from(checked).map(cb => cb.nextElementSibling.textContent.trim());
                selectedHint.textContent = 'Terpilih: ' + names.join(', ');
                selectedHint.className = 'text-success';
            }
        }
        topicCheckboxes.forEach(cb => cb.addEventListener('change', updateTopicInfo));
        selectAllTopics.addEventListener('click', () => { topicCheckboxes.forEach(cb => cb.checked = true); updateTopicInfo(); });
        clearAllTopics.addEventListener('click', () => { topicCheckboxes.forEach(cb => cb.checked = false); updateTopicInfo(); });
        updateTopicInfo();

        // --- search student ---
        const searchInput = document.getElementById('student-search');
        if (searchInput) searchInput.addEventListener('input', renderStudentList);

        // --- analyze ---
        analyzeBtn.addEventListener('click', analyze);

        async function analyze() {
            const topicIds = Array.from(document.querySelectorAll('.topic-checkbox:checked')).map(cb => cb.value);
            if (topicIds.length === 0) {
                alert('Pilih minimal satu topik untuk dianalisis.');
                return;
            }

            loadingSpinner.style.display = 'block';
            resultsDiv.style.display = 'none';
            studentSection.style.display = 'none';
            exportCsvBtn.style.display = 'none';
            errorDiv.style.display = 'none';
            analyzeBtn.disabled = true;

            try {
                const response = await fetch(`{{ route('api.analytics.spk-recommendations') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': `{{ csrf_token() }}` },
                    body: JSON.stringify({ topic_ids: topicIds })
                });
                const result = await response.json();
                if (!result.success) {
                    errorDiv.textContent = result.error || 'Gagal memuat data';
                    errorDiv.style.display = 'block';
                    return;
                }
                allData = result;
                renderStudents(result);
                renderSummary(result);
                renderAllData(result);
                resultsDiv.style.display = 'block';
                exportCsvBtn.style.display = 'inline-block';
            } catch (e) {
                errorDiv.textContent = 'Error: ' + e.message;
                errorDiv.style.display = 'block';
            } finally {
                loadingSpinner.style.display = 'none';
                analyzeBtn.disabled = false;
            }
        }

        // --- render students with badge+search pattern ---
        function renderStudentList() {
            if (!allData || !allData.students) return;
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            studentList.innerHTML = '';

            // Show selected badges on top (only when not searching)
            if (!q) {
                const selected = allData.students.filter(s => !excludedStudents.has(s.id));
                if (selected.length > 0) {
                    const badgeContainer = document.createElement('div');
                    badgeContainer.className = 'd-flex flex-wrap gap-1';
                    selected.forEach(s => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-1';
                        badge.style.cursor = 'pointer';
                        badge.textContent = s.name;
                        const x = document.createElement('i');
                        x.className = 'fas fa-times ms-1';
                        x.style.pointerEvents = 'none';
                        badge.appendChild(x);
                        badge.onclick = function() { excludedStudents.add(s.id); applyStudentFilter(); };
                        badgeContainer.appendChild(badge);
                    });
                    studentList.appendChild(badgeContainer);
                } else {
                    studentList.innerHTML = '<p class="text-muted text-center w-100">Belum ada mahasiswa dipilih. Ketik nama untuk mencari dan memilih.</p>';
                    applyStudentFilter();
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
                    cb.className = 'form-check-input student-checkbox';
                    cb.type = 'checkbox';
                    cb.value = s.id;
                    cb.checked = !excludedStudents.has(s.id);
                    cb.dataset.name = s.name;
                    cb.addEventListener('change', function() {
                        if (this.checked) excludedStudents.delete(parseInt(this.value));
                        else excludedStudents.add(parseInt(this.value));
                        applyStudentFilter();
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

            applyStudentFilter();
        }

        function renderStudents(data) {
            excludedStudents = new Set();
            if (searchInput) searchInput.value = '';
            studentSection.style.display = 'block';
            renderStudentList();

            document.getElementById('select-all-students').onclick = function() {
                excludedStudents = new Set();
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
                renderStudentList();
            };
            document.getElementById('clear-all-students').onclick = function() {
                allData.students.forEach(s => excludedStudents.add(s.id));
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                renderStudentList();
                if (searchInput) searchInput.value = '';
            };
            applyStudentFilter();
        }

        function applyStudentFilter() {
            let ids;
            const domCheckboxes = document.querySelectorAll('.student-checkbox');
            if (domCheckboxes.length > 0) {
                ids = new Set(Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => parseInt(cb.value)));
                excludedStudents.forEach(id => ids.delete(id));
            } else {
                ids = new Set(allData.students.map(s => s.id));
                excludedStudents.forEach(id => ids.delete(id));
            }

            const filtered = allData.recommendations.filter(r => ids.has(r.student_id));
            renderTable(filtered);
            updateSummaryCards(filtered);
            updateStudentCount();
        }

        function updateStudentCount() {
            const total = allData ? allData.students.length : 0;
            const count = total - excludedStudents.size;
            document.getElementById('student-filter-count').textContent = count + '/' + total;
        }

        // --- render summary cards (overall) ---
        function renderSummary(data) {
            const recs = data.recommendations || [];
            const total = recs.length;
            const avgDda = total ? (recs.reduce((a, r) => a + r.avg_score_dda, 0) / total).toFixed(2) : 0;
            const avgNon = total ? (recs.reduce((a, r) => a + r.avg_score_non_dda, 0) / total).toFixed(2) : 0;
            const avgImp = total ? (recs.reduce((a, r) => a + r.dda_improvement, 0) / total).toFixed(2) : 0;
            document.getElementById('total-students').textContent = total;
            document.getElementById('avg-dda').textContent = avgDda;
            document.getElementById('avg-non-dda').textContent = avgNon;
            document.getElementById('avg-improvement').textContent = avgImp;
        }

        function updateSummaryCards(filtered) {
            const total = filtered.length;
            const avgDda = total ? (filtered.reduce((a, r) => a + r.avg_score_dda, 0) / total).toFixed(2) : 0;
            const avgNon = total ? (filtered.reduce((a, r) => a + r.avg_score_non_dda, 0) / total).toFixed(2) : 0;
            const avgImp = total ? (filtered.reduce((a, r) => a + r.dda_improvement, 0) / total).toFixed(2) : 0;
            document.getElementById('total-students').textContent = total;
            document.getElementById('avg-dda').textContent = avgDda;
            document.getElementById('avg-non-dda').textContent = avgNon;
            document.getElementById('avg-improvement').textContent = avgImp;
        }

        // --- render table ---
        function renderTable(recs) {
            const tbody = document.getElementById('recommendations-body');
            const noData = document.getElementById('no-rec-data');
            const recCount = document.getElementById('rec-count');

            if (!recs || recs.length === 0) {
                tbody.innerHTML = '';
                noData.style.display = 'block';
                recCount.textContent = '0 data';
                return;
            }
            noData.style.display = 'none';
            recCount.textContent = recs.length + ' data';

            function formatTopicWithLevel(topic, levels) {
                const lv = levels && levels[topic] !== undefined ? levels[topic] : '?';
                const lvNum = parseInt(lv);
                let label = '', cls = '';
                if (lvNum >= 3) { label = 'Kuat'; cls = 'bg-success'; }
                else if (lvNum >= 1) { label = 'Sedang'; cls = 'bg-warning text-dark'; }
                else { label = 'Rendah'; cls = 'bg-danger'; }
                return `${topic} <span class="badge ${cls} text-white">${label} (level ${lv})</span>`;
            }

            let html = '';
            recs.forEach(rec => {
                const levelColor = rec.intervention_level === 'high' ? 'danger' : (rec.intervention_level === 'medium' ? 'warning' : 'success');
                const improvementClass = rec.dda_improvement > 0 ? 'text-success' : (rec.dda_improvement < 0 ? 'text-danger' : '');
                const levels = rec.all_topic_levels || {};

                let weakHtml = '';
                if (rec.weak_topics && rec.weak_topics.length) {
                    weakHtml = rec.weak_topics.map(t => formatTopicWithLevel(t, levels)).join('<br>');
                } else {
                    weakHtml = '<span class="text-muted">Tidak ada</span>';
                }

                let strongHtml = '';
                if (rec.strong_topics && rec.strong_topics.length) {
                    strongHtml = rec.strong_topics.map(t => formatTopicWithLevel(t, levels)).join('<br>');
                } else {
                    strongHtml = '<span class="text-muted">Tidak ada</span>';
                }

                html += `
                    <tr>
                        <td><strong>${rec.student_name}</strong></td>
                        <td class="text-center">${rec.avg_score_dda}</td>
                        <td class="text-center">${rec.avg_score_non_dda}</td>
                        <td class="text-center ${improvementClass}">${rec.dda_improvement > 0 ? '+' : ''}${rec.dda_improvement}</td>
                        <td>${weakHtml}</td>
                        <td>${strongHtml}</td>
                        <td class="text-center"><span class="badge text-white bg-${levelColor} badge-intervention">${rec.intervention_level}</span></td>
                        <td>${rec.recommendation}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary view-report-btn me-1" data-id="${rec.student_id}" data-name="${rec.student_name}" title="Rapor DDA">
                                <i class="fas fa-robot"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success view-course-report-btn" data-id="${rec.student_id}" data-name="${rec.student_name}" title="Rapor Mata Kuliah">
                                <i class="fas fa-graduation-cap"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Attach event listeners for report buttons
            document.querySelectorAll('.view-report-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    const student = allData.recommendations.find(r => r.student_id === id);
                    if (student) showReport(student);
                });
            });
            document.querySelectorAll('.view-course-report-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    showCourseReport(id);
                });
            });
        }

        // --- show report modal ---
        let reportData = null;

        function showReport(student) {
            reportData = student;
            const content = document.getElementById('report-content');
            const levels = student.all_topic_levels || {};
            const improvement = student.dda_improvement;
            const level = student.intervention_level;
            const bestMethod = student.avg_score_dda > student.avg_score_non_dda ? 'DDA' : (student.avg_score_dda < student.avg_score_non_dda ? 'Non-DDA' : 'Seimbang');
            const ddaEffective = student.avg_score_dda > student.avg_score_non_dda;

            function topicBadgeHtml(topicsArr, isStrong) {
                if (!topicsArr || !topicsArr.length) return '<span class="text-muted">Tidak ada</span>';
                return topicsArr.map(t => {
                    const lv = levels[t] !== undefined ? parseInt(levels[t]) : 0;
                    const label = isStrong ? 'Kuat' : (lv <= 1 ? 'Rendah' : 'Sedang');
                    const cls = isStrong ? 'bg-success' : (lv <= 1 ? 'bg-danger' : 'bg-warning text-dark');
                    return `<span class="badge ${cls} me-1 mb-1 d-inline-block p-2">${t}: ${label} (level ${lv})</span>`;
                }).join('');
            }

            const maxScore = Math.max(student.avg_score_dda, student.avg_score_non_dda, 1);
            const ddaPct = (student.avg_score_dda / maxScore) * 100;
            const nonPct = (student.avg_score_non_dda / maxScore) * 100;

            content.innerHTML = `
                <div id="report-card" style="font-family: 'Segoe UI', Arial, sans-serif;">
                    <!-- Header Rapor -->
                    <div class="text-center mb-4 pb-3" style="border-bottom: 3px solid #4e73df;">
                        <h3 class="text-primary mb-1">RAPOR MAHASISWA</h3>
                        <p class="text-muted mb-0">Laporan Hasil Evaluasi Pembelajaran Adaptif</p>
                    </div>

                    <!-- Identitas -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h4 class="mb-0">${student.student_name}</h4>
                            <small class="text-muted">Laporan performa belajar dengan sistem DDA</small>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-${level === 'high' ? 'danger' : (level === 'medium' ? 'warning' : 'success')} p-2 fs-6">${level === 'high' ? 'Intervensi Tinggi' : (level === 'medium' ? 'Monitoring' : 'Performa Baik')}</span>
                        </div>
                    </div>

                    <!-- Score Comparison Visual -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2"><i class="fas fa-chart-bar me-2"></i>Perbandingan Skor</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <div class="text-muted small">Rata-rata Skor DDA</div>
                                    <div class="display-6 fw-bold text-success">${student.avg_score_dda}</div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: ${ddaPct}%"></div>
                                    </div>
                                    <div class="mt-2"><span class="badge bg-success">${student.dda_attempts}x percobaan</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger h-100">
                                <div class="card-body text-center">
                                    <div class="text-muted small">Rata-rata Skor Non-DDA</div>
                                    <div class="display-6 fw-bold text-danger">${student.avg_score_non_dda}</div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-danger" style="width: ${nonPct}%"></div>
                                    </div>
                                    <div class="mt-2"><span class="badge bg-danger">${student.non_dda_attempts}x percobaan</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kesimpulan DDA -->
                    <div class="card mb-4 ${ddaEffective ? 'border-success' : 'border-warning'}">
                        <div class="card-body text-center">
                            <h5><i class="fas ${ddaEffective ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning'} me-2"></i> 
                                Metode Terbaik: <strong class="${ddaEffective ? 'text-success' : 'text-warning'}">${bestMethod}</strong>
                            </h5>
                            <p class="mb-0">
                                ${ddaEffective 
                                    ? 'DDA memberikan nilai lebih tinggi dibanding Non-DDA. Sistem DDA <strong>bermanfaat</strong> untuk mahasiswa ini.'
                                    : 'Non-DDA memberikan nilai lebih tinggi. Sistem DDA mungkin perlu disesuaikan lebih lanjut.'}
                            </p>
                            <p class="small text-muted mt-1">Peningkatan DDA: ${improvement > 0 ? '+' : ''}${improvement} poin</p>
                        </div>
                    </div>

                    <!-- Topik -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Topik Lemah (level 0-2)</h5>
                            <div>${topicBadgeHtml(student.weak_topics, false)}</div>
                            ${(!student.weak_topics || !student.weak_topics.length) ? '<p class="text-muted">Tidak ada topik lemah</p>' : ''}
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 text-success"><i class="fas fa-check-circle me-2"></i>Topik Kuat (level 3-4)</h5>
                            <div>${topicBadgeHtml(student.strong_topics, true)}</div>
                            ${(!student.strong_topics || !student.strong_topics.length) ? '<p class="text-muted">Tidak ada topik kuat</p>' : ''}
                        </div>
                    </div>

                    <!-- Rekomendasi -->
                    <div class="card bg-light mb-0">
                        <div class="card-body">
                            <h5 class="text-primary"><i class="fas fa-lightbulb me-2"></i>Rekomendasi</h5>
                            <p class="mb-0">${student.recommendation}</p>
                        </div>
                    </div>
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            modal.show();
        }

        // --- show course report ---
        async function showCourseReport(studentId) {
            const content = document.getElementById('course-report-content');
            content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Memuat data...</p></div>';
            const modal = new bootstrap.Modal(document.getElementById('courseReportModal'));
            modal.show();

            try {
                const res = await fetch(`{{ route('api.analytics.spk-student-report') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': `{{ csrf_token() }}` },
                    body: JSON.stringify({ student_id: studentId })
                });
                const data = await res.json();
                if (!data.success) {
                    content.innerHTML = `<div class="alert alert-danger">${data.error || 'Gagal memuat data'}</div>`;
                    return;
                }

                let html = `
                    <div style="font-family: 'Segoe UI', Arial, sans-serif;">
                        <div class="text-center mb-4 pb-3" style="border-bottom: 3px solid #28a745;">
                            <h3 class="text-success mb-1">RAPOR MATA KULIAH</h3>
                            <p class="text-muted mb-0">${data.student_name}</p>
                        </div>
                `;

                if (!data.report || data.report.length === 0) {
                    html += '<div class="alert alert-info">Belum ada data mata kuliah.</div>';
                } else {
                    function gradeColor(grade) {
                        if (grade === 'A' || grade === 'A-') return 'success';
                        if (grade === 'B+' || grade === 'B' || grade === 'B-') return 'primary';
                        if (grade === 'C+' || grade === 'C') return 'warning text-dark';
                        return 'danger';
                    }

                    function topicBadge(topic, level) {
                        const lv = parseInt(level);
                        let label = '', cls = '';
                        if (lv >= 3) { label = 'Kuat'; cls = 'success'; }
                        else if (lv >= 1) { label = 'Sedang'; cls = 'warning text-dark'; }
                        else { label = 'Rendah'; cls = 'danger'; }
                        return `<span class="badge bg-${cls} text-white me-1">${topic}: ${label} (level ${lv})</span>`;
                    }

                    // IPK Header
                    const ipkColor = data.ipk >= 3.00 ? 'success' : (data.ipk >= 2.00 ? 'warning text-dark' : 'danger');
                    html += `
                        <div class="text-center mb-4">
                            <div class="d-inline-block p-3 rounded-circle bg-${ipkColor} text-white" style="width: 100px; height: 100px; line-height: 74px; font-size: 28px; font-weight: bold; border: 4px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                                ${data.ipk}
                            </div>
                            <div class="mt-2"><strong>IPK</strong> (${data.total_courses} mata kuliah)</div>
                        </div>
                    `;

                    data.report.forEach(course => {
                        const gradeCls = gradeColor(course.grade);

                        let allTopicsHtml = '';
                        if (course.all_topic_levels && Object.keys(course.all_topic_levels).length) {
                            allTopicsHtml = Object.entries(course.all_topic_levels)
                                .map(([t, l]) => topicBadge(t, l))
                                .join(' ');
                        } else {
                            allTopicsHtml = '<span class="text-muted">Belum ada data topik</span>';
                        }

                        html += `
                            <div class="card mb-3 border-${gradeCls}">
                                <div class="card-header bg-${gradeCls} text-white d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-book me-2"></i>${course.course_name}</strong>
                                    <div>
                                        <span class="badge bg-light text-dark fs-6 me-2">${course.avg_score}</span>
                                        <span class="badge bg-white text-${gradeCls} fs-6">${course.grade}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Topik Paling Kuat:</strong></p>
                                            ${course.strongest_topic ? topicBadge(course.strongest_topic, course.strongest_level) : '<span class="text-muted">-</span>'}
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Topik Paling Lemah:</strong></p>
                                            ${course.weakest_topic ? topicBadge(course.weakest_topic, course.weakest_level) : '<span class="text-muted">-</span>'}
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="mb-1"><strong>Semua Topik:</strong></p>
                                    <div>${allTopicsHtml}</div>
                                </div>
                            </div>
                        `;
                    });
                }

                html += '</div>';
                content.innerHTML = html;
            } catch (e) {
                content.innerHTML = `<div class="alert alert-danger">Error: ${e.message}</div>`;
            }
        }

        // --- download report (PDF) ---
        document.getElementById('download-report-btn').addEventListener('click', function() {
            if (!reportData) return;
            const element = document.getElementById('report-card');
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'rapor_' + reportData.student_name.replace(/\s+/g, '_') + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });

        // --- render all (init) ---
        function renderAllData(data) {
            applyStudentFilter();
        }

        // --- export CSV (all data) ---
        exportCsvBtn.addEventListener('click', function() {
            if (!allData || !allData.recommendations || allData.recommendations.length === 0) {
                alert('Tidak ada data untuk diekspor.');
                return;
            }
            let csv = 'Nama Mahasiswa,Rata-rata Skor DDA,Rata-rata Skor Non-DDA,Peningkatan DDA,Topik Lemah,Topik Kuat,Level Intervensi,Rekomendasi\n';
            allData.recommendations.forEach(rec => {
                const weak = rec.weak_topics && rec.weak_topics.length ? rec.weak_topics.join(';') : 'Tidak ada';
                const strong = rec.strong_topics && rec.strong_topics.length ? rec.strong_topics.join(';') : 'Tidak ada';
                csv += `"${rec.student_name}",${rec.avg_score_dda},${rec.avg_score_non_dda},${rec.dda_improvement},"${weak}","${strong}",${rec.intervention_level},"${rec.recommendation}"\n`;
            });
            const link = document.createElement('a');
            link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
            link.download = 'spk_recommendations.csv';
            link.click();
        });
    });
</script>
@endsection
