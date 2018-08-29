var marginRight = 90;	// plot to chart edge (to align right side)

var series_no = 5;
var title_height = 45;
var series_spacing = 24;

var optionsMeds = {
  chart: {
    className: 'oes-chart-medications-both',	// suffix: -right -left or -both (eyes)
    height: title_height + series_no * series_spacing,
    marginRight: marginRight,					// plot to chart edge (align right side)
    spacing: [15, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
    type: 'columnrange', 				// Can be any of the chart types listed under plotOptions. ('line' default)
    inverted: true,
  },

  credits: { enabled: false },  // highcharts url (logo) removed
  exporting: false,

  title: {
    text: 'Medications, IOP, VA & MD',
    align: 'center',
    margin:0,
    y:0, 				// title needs offset to not go under legend in small mode
  },

  xAxis: {
    visible: false,
  },

  yAxis: {
    title:'',
    type: 'datetime',
    labels: {
      enabled:false,
    },
    crosshair: {
      snap:false,
    },
    startOnTick: false,
    endOnTick: false,
    tickPixelInterval: 100,  // if this is too high the last tick isn't shown (default 100)
  },
  tooltip: {
    useHtml: true,
    formatter: function () {
      var stop_reason = '';
      if(this.point.stop_reason){
        stop_reason = '<br/><strong> Stop Reason:'+ this.point.stop_reason +'</strong>'
      }
      return '<strong>' + this.point.name + '</strong><br /><strong>'
        + Highcharts.dateFormat('%d/%m/%Y', this.point.low) + ' - '
        + Highcharts.dateFormat('%d/%m/%Y', this.point.high)+ '</strong>'
        + stop_reason;
    }
  },

  plotOptions: {
    columnrange: {
      animation: {
        duration: 0, // disable the inital draw animation
      },
      dataLabels: {
        className:"oes-hs-columnrange-label",
        enabled: true,
        inside: false,			// move labels outside of the column area
        crop: false,
        padding:0,				// needs to be 0, or else SVG rect shows up with the CSS
        allowOverlap: true,
        formatter: function () {
          if (this.point.point_index == 0 && this.y ==this.point.low){
            return this.point.name;
          }
        },
      },
      showInLegend:false, 	// no legend
      groupPadding:0,			// effects the column 'height' (0.2 default)
    }
  },

  legend: {
    enabled: false
  },

};

function setSeriesNo(length){
  series_no = length;
}

function drawMedsSeries(chart, data, eye_side){
  chart.setSize(null ,title_height + series_no * series_spacing);
  var options = {
    className: "oes-hs-eye-"+eye_side+"-dull",
    pointWidth: "20",
    keys: ['low','high','stop_reason']
  };
  var data_list = [];
  for (name in data){
    for (i in data[name]){
        data_list.push({
          x: chart.xAxis[0]['categories'].indexOf(name),
          'low':data[name][i]['low'],
          'high':data[name][i]['high'],
          'name': name,
          'point_index': i,
          'stop_reason': data[name][i]['stop_reason']});
      }
  }

  //fill empty columns to make sure both eye side display equal height.
  for (var j = Object.keys(data).length; j < series_no; j++){
    data_list.push({x: j, 'low': null, 'high': null, 'stop_reason':''});
  }
  addSeries(chart, '', data_list, options );

}