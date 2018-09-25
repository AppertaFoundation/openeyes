var crt_yaxis = {
  side: 'left',
  title: 'CRT(um)',
  range: [],
};

var va_yaxis = {
  side: 'right',
  title: 'VA',
  range: [-100, 150],
};

function setYAxis_MR(options){
  return  {
    side: options['side'],
    title: options['title'],
    range: options['range'],
    /* Grid line settings of yaxis */
    showgrid: true,
    gridcolor: '#8c8c8c',

    /*Ticks setting of yaxis*/
    ticks: 'outside',
    showticklabels: true,
  };
}

function setMRFlags_options(options){
  return {
    type: 'rect',
    xref: 'x',
    yref: 'y',
    x0: options['x0'],
    y0: options['y0'],
    x1: options['x1'],
    y1: options['y1'],
    fillcolor: options['color'],
    line: {
      width: 0
    }
  };
}