google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // demo data
    const demo = [
        ['Date', 'widgets', 'clicks', 'sales'],
        ['2004',  1000,      400],
        ['2005',  1170,      460],
        ['2006',  660,       1120],
        ['2007',  1030,      540]
    ]
    const options = {
        curveType: 'function',
        legend: { position: 'bottom' },
        width: '100%',
    };

    const salesChart = new google.visualization.LineChart(document.getElementById('sales-chart'));
    const promoterChart = new google.visualization.LineChart(document.getElementById('promoter-chart'));

    salesChart.draw(google.visualization.arrayToDataTable(cartlassi_chart.data.sales), {title: 'Sellings Stats', ...options});
    promoterChart.draw(google.visualization.arrayToDataTable(cartlassi_chart.data.promoter), {title: 'Promoting Stats', ...options});
}