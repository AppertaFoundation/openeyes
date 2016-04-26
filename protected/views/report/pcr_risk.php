

<div id="pcr-risk" style="height: 400px; min-width: 310px; max-width: 600px; margin: 0 auto"></div>

<script type="text/javascript">
    $('#pcr-risk').highcharts({
        credits: {
            enabled: false
        },
        title: {
            text: 'Cataract Audit',
            x: -20 //center
        },
        subtitle: {
            text: 'Case Complexity Adjusted PCR Rate',
            x: -20
        },
        xAxis: {
            title: {
                text: 'No. Operations'
            }
        },
        yAxis: {
            title: {
                text: 'Case Complexity Adjusted CR Rupture Rate'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }, {
                value: 1.95,
                color: 'yellow',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'Average'
                }
            }],
            max: 30
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Upper 98%',
            data: [[0,100], [100,15.67042149], [200,8.80658139], [300,6.73926036], [400,5.73078072],  [500,5.12580471], [600,4.71852291], [700,4.42342034], [800,4.1984406], [900,4.02041122], [1000,3.87547218]]
        }, {
            name: 'Upper 95%',
            data: [[0,100], [100,7.58459394], [200,5.14002939], [300,4.31373999], [400, 3.88317448],  [500, 3.61334183], [600,3.4258317], [700,3.28661537], [800,3.1783895], [900,3.09136168], [1000,3.01954389]]
        },{
            name: 'Current Surgeon',
            type: 'scatter',
            data: [[225,2.8]]
        }]
    });
</script>