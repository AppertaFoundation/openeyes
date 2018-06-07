// highSTOCK https://api.highcharts.com/highstock/

/*
* Positioning of Elements below the xAxis.
* The xAxis is offset to allow space for the banners
* Banners and data Flags are drawn from the xAxis up.
*/

var drugs = [];	// drug banners
var bannersOffset = 45 * drugs.length; 			// offset xAxis to allow space for drug banners
var xAxisOffset ; 			// allow for the '>5' flags
var flagYoffset = -40; 							// drug flags are positioned relative to xAxis
var total_height = 700;
var eye_side = 'right';
var eye_side_label = 'R';
var octImgStack;
/*
* Highchart options (data and positioning only)
* all UI stylng is handled in CSS
*/
var options_MR = {
  chart: {
    events: {
      load: function() {
        highHelp.drawBanners(this,Object.keys(drugs[eye_side]));
      },
      redraw: function(e){
        if ($(this['renderTo']).hasClass('highcharts-right')){
          side = 'right';
        } else {
          side = 'left';
        }

        highHelp.drawBanners(this,Object.keys(drugs[side]));
      }
    },
    className: 'oes-chart-mr-'+eye_side,	// suffix: -right -left or -both (eyes)
    height: total_height, 						// chart height fixed px
    marginTop:80,						// make px space for Legend
    spacing: [30, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
    type: 'line' 						// Can be any of the chart types listed under plotOptions. ('line' default)
  },

  credits: { enabled: false },  // highcharts url (logo) removed
  exporting: false,

  title: 	{
    text: "Retinal thickness-Visual acuity",
    align: 'center',
    margin:60,
    y:-10, // title needs offset to not go under legend in small mode
  },

  // standard settings
  legend: 		highHelp.chartLegend(),
  navigator: 		highHelp.chartNavigator(),
  rangeSelector: 	highHelp.chartRangeSelector(-60,-25),	// offset from bottom right (x,y) "Show all" button

  yAxis: [{
    // primary y axis
    title: {
      text: 'CRT (um)'
    },
    opposite: true,
    reversed: false,
    height: total_height - bannersOffset - 430
  },{
    // secondary y axis
    title: {
      text: 'VA ()'
    },
    min: 1,
    max: 150,
    opposite: false,
    height: total_height - bannersOffset - 430
  }],

  xAxis: {
    type: 'datetime',
    title: {
      text: 'Time'
    },
    crosshair: {
      snap: false,		// blue line smooth
    },
    labels: {
      y:30				// move labels below ticks
    },
    offset: xAxisOffset,   	// this moves the chart up to allow for the banners and other flags
    tickPixelInterval: 50,  // if this is too high the last tick isn't shown (default 100) but depends on chart width
  },

  plotOptions: {
    series: {
      animation:false,
      point: {

      },

      label: {
        enabled:false,
      },

      marker: {
        symbol:'circle',
      }
    },

    flags: {
      shape: "square",
      showInLegend: false,
      tooltip: {
        pointFormatter : function () {
          var s = '<b>'+this.info+'</b>';
          return s;
        }
      }
    }
  },

  // ----------------------  Medical Retina Data
  series: []
};

function changeSetting(enter_drugs, side) {
  drugs = enter_drugs;
  bannersOffset = 45 * drugs.length; 			// offset xAxis to allow space for drug banners
  xAxisOffset = bannersOffset + 10; 			// allow for the '>5' flags
  eye_side = side;
  eye_side_label = (eye_side=='right')?'R':'L';
}

function drawMRSeries(chart_MR, VA_data, CRT_data, VA_lines_data, injections_data, axis_type){
  var VA_options = {
    type: 'line',
    colorIndex: (eye_side=='right')?11:21,
    yAxis: 1,
    showInNavigator: true
  };
  var CRT_options = {
    type: 'line',
    colorIndex: (eye_side=='right')?12:22,
    yAxis: 0,
    showInNavigator: true
  };
  var VA_lines_options = {
    type: "flags",
    className: 'oes-hs-eye-'+eye_side+'-dull',
    y: (0 - xAxisOffset - 15)
  };
  addSeries(chart_MR, '(VA)'+axis_type+'  ('+eye_side_label+')', VA_data[eye_side], VA_options);
  addSeries(chart_MR, 'CRT ('+eye_side_label+')',  CRT_data[eye_side], CRT_options);
  addSeries(chart_MR, 'VA > 5 lines',  VA_lines_data[eye_side], VA_lines_options);
  var i = 0;
  for ( var injection_name in injections_data[eye_side]) {
    var injections_options = {
      type: "flags",
      className: 'oes-hs-eye-'+eye_side+'-dull',
      y: flagYoffset-i*40,
    };
    var size = injections_data[eye_side][injection_name].length;
    addSeries(chart_MR, injection_name+"("+size+")", injections_data[eye_side][injection_name], injections_options);
    i++;
  }
}

function setImgStack(container,img_id_prefix, initID, callBack) {
  octImgStack = new initStack(container, img_id_prefix, initID, callBack);
  options_MR['plotOptions']['series']['point'] = {
    events: {
      mouseOver: function( e ){
        octImgStack.setImg( this.oct, this.side ); // link chart points to OCTs
      }
    }
  }
}