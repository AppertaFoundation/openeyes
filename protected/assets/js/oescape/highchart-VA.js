var marginRight = 90;	// plot to chart edge (to align right side)

var optionsVA = {
  chart: {
    height:400,
    marginRight: marginRight,			// plot to chart edge (align right side)
    spacing: [15, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
  },

  credits: 		{ enabled: false },  // highcharts url (logo) removed
  scrollbar: 		{ enabled: false },

  exporting: false,

  title: {
    align: 'center',
    text: '',
  },

  legend: highHelp.chartLegend(),

  tooltip: {
    shared: false,
    useHtml: true,
    formatter: function(){
      return OEScape.toolTipFormatters.VA(this);
    },
  },

  navigator: {
    enabled: true,
    xAxis: {
      labels : {
        enabled: false,
      }
    }
  },

  rangeSelector: 	highHelp.chartRangeSelector(-42,-5),	// offset from bottom right (x,y) "Show all" button

  plotOptions: {
    series: {
      animation: false,
      marker: {
        enabled: true,
        radius: 4,
        symbol: "circle"
      },
    },
    line: {
      showInLegend: true
    }
  },

  xAxis: {
    type: 'datetime',
    title: {
      text: '',
    },
    crosshair: {
      snap:false,
    },
    labels: {
      y:25				// move labels below ticks
    },
    tickPixelInterval: 100,  // if this is too high the last tick isn't shown (default 100)
    startOnTick: false, //If the charts are forced to start and end on ticks they can't align properly
    endOnTick: false
  },

  yAxis: [{
    title: {
      text: ''
    },
    opposite: true,
    reversed: false,
    labels: {
    },

  },{
    title: {
      text: ''
    },
    opposite: true,
    reversed: false,
    labels: {
      format: '{value} dB',
    }
  }]
};


function drawVASeries(chart, data, eye_side) {
  var series_option_1 = {
    type:'line',
    colorIndex: (eye_side=='right')?11:21,				// Right Eye 11-13: 11 - solid; 12 - dotted; 13 - dashed
    yAxis:0,
    showInNavigator: true,
  };
  addSeries(chart, 'VA ('+eye_side+')', data, series_option_1);
}


