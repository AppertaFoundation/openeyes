function setYAxis_VA(options) {
  return {
    side: 'right',
    title: {
              text: 'VA ('+ $('#va_history_unit_id').children("option:selected").text() +')',
              font: {
                color: 'white'
              }
            },
    range: options['range'],
    /* Grid line settings of yaxis */
    showgrid: false, // Show only VFI grids for now
    gridwidth: 0.25,
    gridcolor: '#444',

    /*Ticks setting of yaxis*/
    ticks: 'outside',
    // set y tick white
    tickfont: {
      color: '#fff',
    },
    showticklabels: true,
    tickvals: options['tickvals'],
    ticktext: options['ticktext'],
    
    /*spike setting aka Cursor*/
    showspikes: true,
    spikecolor: '#3db0fb',
    spikethickness: 1,
    spikedash:'line',
    spikemode: 'across',
    spikesnap: 'cursor',
  };
}