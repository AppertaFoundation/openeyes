var marker_line_plotly_options = {
  type: 'line',
  xref: 'x',
  yref: 'y',
  line: {
    dash: 'dot',
  }
};

var marking_annotations = {
  xref: 'x',
  yref: 'y',
  text: '',
  textangle: '90',
  showarrow: false,
  font: {
    color: '#3db0fb',
  },
  bgcolor: '#141e2b',
};

var trace_hoverlabel = {
  bgcolor: '#141e2b',
    bordercolor: '#3db0fb',
    font: {
    color:'#ffffff',
  },
};

var layout_plotly = {
  title: '',
  autosize: false,
  height: 800,
  margin:{
    l:50,
    t:30,
    pad:4,
  },
  paper_bgcolor: 'rgba(0,0,0,0)',
  plot_bgcolor: 'rgba(0,0,0,0)',
  /* legend settings */
  showlegend: true,
  legend: {
    x: 0 ,
    y: 1.1,
    traceorder: 'normal',
    font: {
      family: 'sans-serif',
      size: 12,
      color: '#8c8c8c',
    },


  },
  xaxis: {
    showgrid: false,
    /*Ticks setting*/
    ticks: 'outside',
    showticklabels: true,
  },
  yaxis:{},
  shapes: [],
  annotations: [],
};


var options_plotly = {
  displayLogo: false,
  displayModeBar: false,
};

function setMarkingEvents_plotly(layout, options, annotation, data, side, y_start, y_end){
  for (var key in data[side]) {
    for (var item of data[side][key]) {
      var current_marker_line = JSON.parse(JSON.stringify(options));
      current_marker_line['x0'] = new Date(item);
      current_marker_line['x1'] = new Date(item);
      current_marker_line['y0'] = y_start;
      current_marker_line['y1'] = y_end;
      current_marker_line['line']['color'] = (side === 'right') ? '#9fec6d' : '#fe6767';
      layout['shapes'].push(current_marker_line);


      var current_annotation = JSON.parse(JSON.stringify(annotation));
      current_annotation['x']=new Date(item);
      current_annotation['y']= y_end;
      current_annotation['text']=key;
      layout['annotations'].push(current_annotation);
    }
  }
}
