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
    var options = {
        title: 'Shop Stats',
        curveType: 'function',
        legend: { position: 'bottom' },
        width: '100%',
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart'));

    chart.draw(google.visualization.arrayToDataTable(cartlassi_chart.data), options);
}