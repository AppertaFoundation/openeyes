var marginRight = 90;	// plot to chart edge (to align right side)

var optionsVA = {
  chart: {
    height:400,
    marginRight: marginRight,			// plot to chart edge (align right side)
    spacing: [15, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
  },

  credits: 		{ enabled: false },  // highcharts url (logo) removed
  scrollbar: 		{ enabled: false },

  title: {
    align: 'center',
    text: '',
  },

  legend: highHelp.chartLegend(),

  tooltip: {
    shared:false,
    xDateFormat: '%d/%m/%Y',
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
  },

  yAxis: [{
    title: {
      text: ''
    },
    opposite: true,
    reversed: false,
    min: 1,
    max: 150,
    labels: {
    }

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

  var series_option_2 = {
    type:'line',
    colorIndex: (eye_side=='right')?12:22,				// Right Eye 11-13: 11 - solid; 12 - dotted; 13 - dashed
    yAxis:1,
    showInNavigator: false,
  };

  //MD (mean deviation) values should be taken from visual field data, will not be available until Sept/Oct 2018
  // The below are some demo data.
  var data_2 = [
    [Date.UTC(2013, 03,05),-3.5],
    [Date.UTC(2013, 7,23),-4.5],
    [Date.UTC(2013, 12,3),-4.5],
    [Date.UTC(2014, 5,20),-6.7],
    [Date.UTC(2014, 10,21),-9.2],
    [Date.UTC(2015, 3,24),-6.2],
    [Date.UTC(2015, 6,23),-5.7],
    [Date.UTC(2015, 11,17),-6.2],
    [Date.UTC(2016, 11,25),-8.9],
    [Date.UTC(2017, 6,05),-14.8]
  ];

  addSeries(chart, 'MD ('+eye_side+')', data_2, series_option_2);
}


