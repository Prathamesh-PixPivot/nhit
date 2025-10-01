/**
 * Dashboard Charts Initialization
 * Handles ECharts pie chart rendering for dashboard
 */

// Initialize chart A
function initChartA(chartData) {
    if (!chartData || !Array.isArray(chartData)) {
        console.warn('Chart A: Invalid data provided');
        return;
    }

    const total = chartData.reduce((sum, item) => sum + (item.value || 0), 0);
    const chartElement = document.querySelector('#trafficCharta');
    
    if (!chartElement) {
        console.warn('Chart A: Element #trafficCharta not found');
        return;
    }

    echarts.init(chartElement).setOption({
        tooltip: { trigger: 'item' },
        legend: { top: '5%', left: 'center' },
        series: [{
            name: 'Expense Approval Note',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '60%'],
            avoidLabelOverlap: false,
            label: {
                show: true,
                position: 'center',
                formatter: 'Total\n' + total,
                fontSize: 16,
                fontWeight: 'bold',
                color: '#555'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                }
            },
            labelLine: { show: false },
            data: chartData
        }]
    });
}

// Initialize chart B
function initChartB(chartDataB) {
    if (!chartDataB || !Array.isArray(chartDataB)) {
        console.warn('Chart B: Invalid data provided');
        return;
    }

    const totalB = chartDataB.reduce((sum, item) => sum + (item.value || 0), 0);
    const chartElement = document.querySelector('#trafficChartb');
    
    if (!chartElement) {
        console.warn('Chart B: Element #trafficChartb not found');
        return;
    }

    echarts.init(chartElement).setOption({
        tooltip: { trigger: 'item' },
        legend: { top: '5%', left: 'center' },
        series: [{
            name: 'Expense Approval Note',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '60%'],
            avoidLabelOverlap: false,
            label: {
                show: true,
                position: 'center',
                formatter: 'Total\n' + totalB,
                fontSize: 16,
                fontWeight: 'bold',
                color: '#555'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                }
            },
            labelLine: { show: false },
            data: chartDataB
        }]
    });
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Charts will be initialized by inline scripts with data
    console.log('Dashboard charts module loaded');
});
