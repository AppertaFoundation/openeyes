var crt_yaxis = {
  side: 'left',
  title: 'CRT(um)',
  domain: [0.35, 1],
  showticklabels: true,
  showgrid: true,
};

var va_yaxis = {
  side: 'right',
  title: 'VA',
  range: [-15, 150],
  domain: [0.35,1],
  showticklabels: true,
  showgrid: true,
};

var flags_yaxis = {
  side: 'left',
  title: 'injections',
  domain: [0, 0.3],
  showticklabels: false,
  showgrid: false,
};

function setYAxis_MR(options){
  return  {
    side: options['side'],
    title: options['title'],
    range: options['range'],
    /* Grid line settings of yaxis */
    showgrid: options['showgrid'],
    gridcolor: '#8c8c8c',

    /*Ticks setting of yaxis*/
    ticks: 'outside',
    showticklabels: options['showticklabels'],
    domain: options['domain'],
  };
}

function setMRFlags_options(options){
  return {
    type: 'rect',
    xref: 'x',
    yref: 'y3',
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