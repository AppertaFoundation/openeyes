var marginRight = 90;	// plot to chart edge (to align right side)

var series_no = 5;

var optionsMeds = {
  chart: {
    className: 'oes-chart-medications-both',	// suffix: -right -left or -both (eyes)
    height: 200, //limited to 5 meds
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

    tickPixelInterval: 100,  // if this is too high the last tick isn't shown (default 100)
  },
  tooltip: {
    useHtml: true,
    formatter: function () {
      var stop_reason = '';
      if(this.point.stop_reason){
        stop_reason = '<br/><strong> Stop Reason:'+ this.point.stop_reason +'</strong>'
      }
      return '<strong>' + this.series.name + '</strong><br /><strong>'
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
        overflow: 'justify',
        padding:5,				// needs to be 0, or else SVG rect shows up with the CSS
        formatter: function () {

          if (this.y == this.point.low) {
            return this.series.name;
          } else {
            return '';		// because of the padding this is still drawn (css hides it)
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
  for (name in data){
    var options = {
      className: "oes-hs-eye-"+eye_side+"-dull",
      pointWidth: "20",
      keys: ['low','high','stop_reason']
    };
    addSeries(chart, name, [data[name]], options );
  }
}