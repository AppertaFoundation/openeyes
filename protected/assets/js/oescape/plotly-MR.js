var crt_yaxis = {
  side: 'left',
  title: 'CRT(um)',
  domain: [0.35, 1],
  showticklabels: true,
  showgrid: false,
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
  domain: [0, 0.3],
  showgrid: false,
  showticklabels: true,
  ticks: 'inside',
};

function setYAxis_MR(options){
  var yaxis_setting = {
    gridwidth: 0.25,
    gridcolor: '#444',
    ticks: 'outside',
  };
  for (var key in options){
    yaxis_setting[key] = options[key];
  }

  return  yaxis_setting;
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
