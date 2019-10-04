var analytics_layout = {
    title: '',
    titlefont: {
        color: '#fff',
    },
    autosize: false,
    margin:{
        l:50,
        t:100,
        b: 50,
        pad:4,
    },
    hovermode:'closest',
    paper_bgcolor: '#101925',
    plot_bgcolor: '#101925',
    /* legend settings */
    showlegend: true,
    legend: {
        x: 0 ,
        y: 1.05,
        traceorder: 'normal',
        font: {
            family: 'sans-serif',
            size: 12,
            color: '#8c8c8c',
        },
    },
    xaxis: {
        titlefont: {
            family: 'sans-serif',
            size: 12,
            color: '#fff',
        },
        showgrid: false,
        /*Ticks setting*/
        ticks: 'outside',
        tickformat: ',d',
        showticklabels: true,
        rangemode: 'tozero',
        showline: true,
        linecolor: '#fff',
        tickcolor: '#fff',
        tickfont: {
            color: '#fff',
        },
        showticksuffix: 'last',
        mirror: true
    },
    yaxis:{
        titlefont: {
            family: 'sans-serif',
            size: 12,
            color: '#fff',
        },
        /*Ticks setting*/
        ticks: 'outside',
        showgrid: true,
        gridcolor: '#8c8c8c',
        showticklabels: true,
        showline: true,
        linecolor: '#fff',
        tickcolor: '#fff',
        tickfont: {
            color: '#fff',
        },
        mirror: true
    },
};

var analytics_options = {
    displayLogo: false,
    displayModeBar: false,
};