var marginRight = 90;	// plot to chart edge (to align right side)

var optionsIOP = {
  chart: {
    height: 250,
    marginRight: marginRight,			// plot to chart edge (align right side)
    spacing: [15, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
  },

  credits: 		{ enabled: false },  // highcharts url (logo) removed
  navigator: 		{ enabled: false },
  scrollbar : 	{ enabled: false },

  title: {
    align: 'center',
    text: '',
  },


  legend: highHelp.chartLegend(),

  exporting: false,

  rangeSelector: {
    enabled: false,
  },

  tooltip: {
    shared:false,
    xDateFormat: '%d/%m/%Y',
  },

  plotOptions: {
    series: {
      animation: {
        duration: 0, // disable the inital draw animation
      },
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

  yAxis: {
    title: {
      text: ''
    },
    opposite: true,
    reversed: false,
    min:0,
  },

  xAxis: {
    className: "oes-hide-xAxis-lines", // can't make visible: false because of the plotlines!
    type: 'datetime',
    title: {
      text: '',
    },

    crosshair: {
      snap:false,
    },

    labels: {
      enabled:true,
      y:25				// move labels below ticks
    },
    tickPixelInterval: 100,  // if this is too high the last tick isn't shown (default 100)

    plotLines: []
  }
};

function drawIOPSeries(chart, data, eye_side){
  var series_option = {
    type:'line',
    colorIndex:(eye_side=='right')?11:21,			// Right Eye 11-13: 11 - solid; 12 - dotted; 13 - dashed
  };

  addSeries(chart, 'IOP ('+eye_side + ')', data, series_option);
}

function setYPlotline(target_value, eye_side){
  return {
    className: 'oes-hs-plotline-'+eye_side,
      value: target_value[eye_side],
    label: {
    text: 'Target IOP ('+eye_side+')',
      align: 'left',
      y: -5,
      x: 0
  },
    zIndex:1,
  };
}

function setXPlotLine(text_value,date,eye_side){
  return {
    className: 'oes-hs-plotline-'+eye_side+'-tight',
    value: date,
    label: {
      text: text_value,
      rotation: 90,
      x: 2,
    },
    zIndex: 1
  };
}

