import $ from 'jquery';
window.$ = window.jQuery = $;

import select2 from 'select2';
select2(window, $);

import 'select2/dist/css/select2.min.css';

document.dispatchEvent(new Event('jquery-ready'));

import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const COLORS = {
    teal:     { bg: 'rgba(43,168,162,0.18)', border: '#2BA8A2', solid: '#2BA8A2' },
    coral:    { bg: 'rgba(239,108,74,0.18)',  border: '#EF6C4A', solid: '#EF6C4A' },
    gold:     { bg: 'rgba(255,210,63,0.22)',  border: '#FFD23F', solid: '#FFD23F' },
    sky:      { bg: 'rgba(93,173,226,0.18)',  border: '#5DADE2', solid: '#5DADE2' },
    tealDark: { bg: 'rgba(30,140,134,0.18)',  border: '#1E8C86', solid: '#1E8C86' },
};

const PALETTE = [COLORS.teal.solid, COLORS.coral.solid, COLORS.gold.solid, COLORS.sky.solid, COLORS.tealDark.solid];
const PALETTE_BG = [COLORS.teal.bg, COLORS.coral.bg, COLORS.gold.bg, COLORS.sky.bg, COLORS.tealDark.bg];

Chart.defaults.font.family = "'Figtree', sans-serif";
Chart.defaults.font.weight = 600;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
Chart.defaults.plugins.legend.labels.padding = 16;
Chart.defaults.plugins.tooltip.backgroundColor = '#1E293B';
Chart.defaults.plugins.tooltip.titleFont = { weight: 700, size: 13 };
Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };
Chart.defaults.plugins.tooltip.padding = { top: 8, bottom: 8, left: 12, right: 12 };
Chart.defaults.plugins.tooltip.cornerRadius = 10;
Chart.defaults.plugins.tooltip.boxPadding = 4;

function initCharts() {
    document.querySelectorAll('[data-chart]').forEach(canvas => {
        const type = canvas.dataset.chart;
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        const colors = JSON.parse(canvas.dataset.colors || 'null');

        const bgColors = colors?.bg || PALETTE.slice(0, values.length);
        const borderColors = colors?.border || PALETTE.slice(0, values.length);

        const config = {
            type,
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: type === 'doughnut' || type === 'pie' ? bgColors : bgColors.map(c => c),
                    borderColor: type === 'doughnut' || type === 'pie' ? '#fff' : borderColors,
                    borderWidth: type === 'doughnut' || type === 'pie' ? 3 : 2,
                    borderRadius: type === 'bar' ? 8 : 0,
                    borderSkipped: false,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: type === 'doughnut' ? '68%' : undefined,
                plugins: {
                    legend: { display: type === 'doughnut' || type === 'pie', position: 'bottom' },
                },
                scales: type === 'bar' || type === 'line' ? {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, stepSize: 1 }, beginAtZero: true },
                } : undefined,
                animation: { duration: 800, easing: 'easeOutQuart' },
            },
        };

        new Chart(canvas, config);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
