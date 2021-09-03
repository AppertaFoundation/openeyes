function setYAxis_VFI(options) {
    return {
        side: 'left',
        title: {
            text: 'VFI',
            font: {
                color: 'white'
            }
        },
        overlaying: 'y',
        range: options['range'],
        showgrid: true,
        gridwidth: 0.25,
        gridcolor: '#444',

        ticks: 'outside',
        tickfont: {
            color: '#fff',
        },
        showticklabels: true,
        tickvals: options['tickvals'],
        ticktext: options['ticktext'],

        showspikes: true,
        spikecolor: '#3db0fb',
        spikethickness: 1,
        spikedash: 'line',
        spikemode: 'across',
        spikesnap: 'cursor',
    };
}