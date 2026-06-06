document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('otaGroupedChart');
    if (!ctx) return;
 
    // Data di-inject dari blade via window object
    const stationLabels = window.REPORTS.stationLabels;
    const rawDatasets   = window.REPORTS.rawDatasets;
    const dateLabels    = window.REPORTS.dateLabels;
 
    const PALETTE = [
        '#4472C4', '#ED7D31', '#A9A9A9', '#FFC000',
        '#5B9BD5', '#70AD47', '#264478', '#9E480E',
        '#636363', '#997300', '#255E91', '#43682B',
    ];
 
    // Re-pivot: dataset per tanggal, data = OTA tiap station
    const pivotDatasets = dateLabels.map((dateLabel, dateIdx) => ({
        label          : dateLabel,
        data           : rawDatasets.map(ds => ds.data[dateIdx] ?? 0),
        backgroundColor: PALETTE[dateIdx % PALETTE.length],
        borderRadius   : 2,
        borderSkipped  : false,
    }));
 
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels  : stationLabels,
            datasets: pivotDatasets,
        },
        options: {
            responsive         : true,
            maintainAspectRatio: false,
            interaction        : { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display : true,
                    position: 'top',
                    labels  : {
                        boxWidth : 14,
                        boxHeight: 14,
                        padding  : 12,
                        font     : { size: 11 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y}%`,
                    },
                },
                datalabels: {
                    anchor   : 'end',
                    align    : 'end',
                    rotation : -90,
                    formatter: (val) => val + '%',
                    font     : { size: 9, weight: 'bold' },
                    color    : '#333',
                    clip     : false,
                },
            },
            layout: {
                padding: { top: 30 },
            },
            scales: {
                x: {
                    grid : { display: false },
                    ticks: { font: { size: 11, weight: 'bold' } },
                },
                y: {
                    min  : 0,
                    max  : 100,
                    grid : { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font    : { size: 11 },
                        callback: (v) => v + '%',
                    },
                },
            },
        },
    });
});
