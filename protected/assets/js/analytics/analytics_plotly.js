const plotly_min_width = 800;
const plotly_min_height = 650;
const page_width = $('#plot').width();
const page_height = $('main').height() - 50;
const layout_width = plotly_min_width > page_width ? plotly_min_width : page_width;
const layout_height = plotly_min_height > page_height ? plotly_min_height : page_height;
const analytics_layout = {
    title: '',
    width: layout_width,
    height: layout_height,
    titlefont: {
        color: '#fff',
    },
    autosize: false,
    margin: {
        l: 50,
        t: 100,
        b: 50,
        pad: 4,
    },
    hovermode: 'closest',
    paper_bgcolor: '#101925',
    plot_bgcolor: '#101925',
    /* legend settings */
    showlegend: true,
    legend: {
        x: 0,
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
    yaxis: {
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

const analytics_options = {
    displayLogo: false,
    displayModeBar: false,
};
