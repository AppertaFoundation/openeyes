var marker_line_plotly_options = {
  type: 'line',
  xref: 'x',
  yref: 'y',
  line: {
    dash: 'dash',
    width: 0.5,
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
  xshift: 10,
  yshift: 40,
  yanchor: 'top',
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
    l:70,
    t:30,
    b: 50,
    pad:4,
  },
  paper_bgcolor: '#141e2b',
  plot_bgcolor: '#141e2b',
  /* legend settings */
  showlegend: true,
  hoverdistance: -1,
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
  hovermode: 'closest',
  spikedistance: -1,
  xaxis: {
    type: 'date',
    showgrid: false,
    /*Ticks setting*/
    ticks: 'outside',
    showticklabels: true,
    showline: true,
    linecolor: '#fff',
    tickcolor: '#fff',
    tickfont: {
      color: '#fff',
    },
    tickangle: 'auto',
    tickformat: '%d/%m/%y',
    /*spike setting*/
    showspikes: true,
    spikecolor: '#3db0fb',
    spikethickness: 1,
    spikedash:'line',
    spikemode: 'across',
    spikesnap: 'cursor',
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
  let current_annotation = [];
  for (var key in data[side]) {
    for (var item of data[side][key]) {
      var current_marker_line = JSON.parse(JSON.stringify(options));
      current_marker_line['x0'] = new Date(item);
      current_marker_line['x1'] = new Date(item);
      current_marker_line['y0'] = y_start;
      current_marker_line['y1'] = y_end;
      current_marker_line['line']['color'] = (side === 'right') ? '#9fec6d' : '#fe6767';
      layout['shapes'].push(current_marker_line);

      if(current_annotation[item] === undefined){
        current_annotation[item] = JSON.parse(JSON.stringify(annotation));
        current_annotation[item]['x']=new Date(item);
        current_annotation[item]['y']= y_end;
        current_annotation[item]['text']=key;
      } else {
        current_annotation[item]['text']+=','+key;
      }
      layout['annotations'].push(current_annotation[item]);
    }
  }
}
