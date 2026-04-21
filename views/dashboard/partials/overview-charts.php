<?php
/**
 * @var string $chartDataJson
 * @var string $chartSummaryText
 */
$chartDataJson = $chartDataJson ?? '{}';
$chartSummaryText = $chartSummaryText ?? '';
?>
<section class="mt-10" aria-labelledby="charts-heading">
    <div class="mb-4">
        <h2 id="charts-heading" class="text-sm font-semibold text-stone-900">Trends</h2>
        <p class="mt-1 text-xs text-stone-500">Charts use live counts from your database.</p>
    </div>

    <p class="sr-only"><?= htmlspecialchars($chartSummaryText, ENT_QUOTES, 'UTF-8') ?></p>

    <div class="grid gap-6 lg:grid-cols-12">
        <figure class="lg:col-span-12 rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <figcaption class="text-sm font-semibold text-stone-900">Publishing &amp; comments</figcaption>
            <p class="mt-1 text-xs text-stone-500">Daily totals for the last 30 days — comments by submission time, posts by publish date.</p>
            <p class="mt-2 text-xs text-stone-600" aria-hidden="true"><?= htmlspecialchars($chartSummaryText, ENT_QUOTES, 'UTF-8') ?></p>
            <div class="relative mt-4 h-72 w-full">
                <canvas id="dashboard-chart-daily" role="img" aria-label="Line chart of comments and published posts per day"></canvas>
            </div>
        </figure>

        <figure class="lg:col-span-6 rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <figcaption class="text-sm font-semibold text-stone-900">Posts by status</figcaption>
            <p class="mt-1 text-xs text-stone-500">Draft, published, and scheduled.</p>
            <div class="relative mx-auto mt-4 h-56 max-w-[18rem] w-full">
                <canvas id="dashboard-chart-post-status" role="img" aria-label="Doughnut chart of post status counts"></canvas>
            </div>
        </figure>

        <figure class="lg:col-span-6 rounded-2xl border border-stone-200/90 bg-white p-5 shadow-sm shadow-stone-900/5 ring-1 ring-stone-200/80">
            <figcaption class="text-sm font-semibold text-stone-900">Comments by status</figcaption>
            <p class="mt-1 text-xs text-stone-500">Moderation queue and outcomes.</p>
            <div class="relative mx-auto mt-4 h-56 max-w-[18rem] w-full">
                <canvas id="dashboard-chart-comment-status" role="img" aria-label="Doughnut chart of comment status counts"></canvas>
            </div>
        </figure>
    </div>
</section>

<script type="application/json" id="dashboard-charts-data"><?= $chartDataJson ?></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function () {
    var el = document.getElementById('dashboard-charts-data');
    if (!el || typeof Chart === 'undefined') return;
    var data;
    try {
        data = JSON.parse(el.textContent || '{}');
    } catch (e) {
        return;
    }

    var gridColor = 'rgba(120, 113, 108, 0.12)';
    var font = { family: 'Inter, ui-sans-serif, system-ui, sans-serif' };

    var daily = document.getElementById('dashboard-chart-daily');
    if (daily && data.dailyLabels && data.dailyComments && data.dailyPublished) {
        new Chart(daily, {
            type: 'line',
            data: {
                labels: data.dailyLabels,
                datasets: [
                    {
                        label: 'Comments',
                        data: data.dailyComments,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.08)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        borderWidth: 2,
                    },
                    {
                        label: 'Published posts',
                        data: data.dailyPublished,
                        borderColor: '#57534e',
                        backgroundColor: 'rgba(87, 83, 78, 0.06)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: font, color: '#44403c', boxWidth: 12 },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(28, 25, 23, 0.92)',
                        titleFont: font,
                        bodyFont: font,
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: font,
                            color: '#78716c',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            font: font,
                            color: '#78716c',
                            precision: 0,
                        },
                    },
                },
            },
        });
    }

    var ps = document.getElementById('dashboard-chart-post-status');
    if (ps && data.postStatus && data.postStatus.labels) {
        new Chart(ps, {
            type: 'doughnut',
            data: {
                labels: data.postStatus.labels,
                datasets: [{
                    data: data.postStatus.data,
                    backgroundColor: data.postStatus.colors,
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: font, color: '#44403c', padding: 12 },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(28, 25, 23, 0.92)',
                        bodyFont: font,
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            },
        });
    }

    var cs = document.getElementById('dashboard-chart-comment-status');
    if (cs && data.commentStatus && data.commentStatus.labels) {
        new Chart(cs, {
            type: 'doughnut',
            data: {
                labels: data.commentStatus.labels,
                datasets: [{
                    data: data.commentStatus.data,
                    backgroundColor: data.commentStatus.colors,
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: font, color: '#44403c', padding: 12 },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(28, 25, 23, 0.92)',
                        bodyFont: font,
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            },
        });
    }
})();
</script>
