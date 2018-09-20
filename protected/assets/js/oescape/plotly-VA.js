var marker_line_plotly_options = {
	type: 'line',
	xref: 'x',
	yref: 'paper',
	y0: 0,
	y1: 1,
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
};

var layout_va_plotly = {
  title: '',
	autosize: false,
	margin:{
  	l:0,
		t:0,
		pad:4,
	},
  height: 800,
  paper_bgcolor: 'rgba(0,0,0,0)',
  plot_bgcolor: 'rgba(0,0,0,0)',
  /* legend settings */
  showlegend: true,
  legend: {
    x: 0,
    y: 1,
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

    rangeslider: {}

  },
  yaxis: {
    range: [-15, 150],
    side: 'right',
    /* Grid line settings of yaxis */
    showgrid: true,
    gridwidth: 0.25,
    gridcolor: '#8c8c8c',

    /*Ticks setting of yaxis*/
    ticks: 'outside',
    showticklabels: true,
  },
	shapes: [],
	annotations: [],
};


var options_va_plotly = {
};

