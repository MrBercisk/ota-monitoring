document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('otaTrendChart');
    if (!ctx) return;
 
    // Data di-inject dari blade via window object
    const trendLabels = window.DASHBOARD.trendLabels;
    const trendOta    = window.DASHBOARD.trendOta;
    const trendDelay  = window.DASHBOARD.trendDelay;
 
    new Chart(ctx, {
        type: 'line',
        data: {
            labels  : trendLabels,
            datasets: [
                {
                    label          : 'OTA %',
                    data           : trendOta,
                    borderColor    : '#696cff',
                    backgroundColor: 'rgba(105,108,255,0.08)',
                    fill           : true,
                    tension        : 0.4,
                    pointRadius    : 0,
                    borderWidth    : 2,
                    yAxisID        : 'y',
                },
                {
                    label          : 'Delay count',
                    data           : trendDelay,
                    borderColor    : '#ff3e1d',
                    backgroundColor: 'transparent',
                    borderDash     : [4, 3],
                    tension        : 0.4,
                    pointRadius    : 0,
                    borderWidth    : 1.5,
                    yAxisID        : 'y2',
                },
            ],
        },
        options: {
            responsive        : true,
            maintainAspectRatio: true,
            interaction       : { mode: 'index', intersect: false },
            plugins           : { legend: { display: false } },
            scales: {
                x : { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                y : { min: 50, max: 100, position: 'left',  ticks: { callback: v => v + '%' } },
                y2: { min: 0,            position: 'right', grid: { display: false } },
            },
        },
    });
});
