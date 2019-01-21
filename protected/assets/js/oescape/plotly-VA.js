function setYAxis_VA(options) {
  return {
    side: 'right',
    title: '',
    range: options['range'],
    /* Grid line settings of yaxis */
    showgrid: true,
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
    ticktext: options['ticktext']
  };
}