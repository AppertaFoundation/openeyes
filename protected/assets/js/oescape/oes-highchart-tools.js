

var highHelp = {

  chartLegend:function (){
    return {
      enabled:true,
      layout: 'vertical',
      align: 'left',
      verticalAlign: 'top',
      width: null,
      floating:true,
      symbolWidth: 40 // make this wide enought to see the line styles
    };
  },

  chartNavigator:function(){
    return  {
      enabled: true,
      xAxis: {
        labels : {
          enabled: false
        }
        },
    };
  },

  chartRangeSelector:function( x, y ){
    return {
      enabled:true,
      inputEnabled: false, 	// hide inputs
      floating: false,
      buttons: [{
        type: 'all',
        text: 'Show all',
      }],
      buttonPosition: {
        align: 'right',
        x:x,				// need to offset position, alignment is all over the place
        y:y,

      },
      buttonTheme: {
        width:60
      },
      verticalAlign: 'bottom'
    };
  },

  // --------------------  Drug Banner Area under xAxis
  // this needs some work to be more flexible but will do for now

  drugBanners:[],

  // banners have to be redrawn on load and redraw
  drawBanners:function( highChart, drugsArr ){

    // remove previous SVGs
    if(highHelp.drugBanners.length){
      for( var i=0; i<highHelp.drugBanners.length; i++){
        highHelp.drugBanners[i].box.element.remove();
        highHelp.drugBanners[i].label.element.remove();
      }
      highHelp.drugBanners = [];
    }

    // draw new SVGs
    for(var i=0; i<drugsArr.length; i++){
      highHelp.buildBanners(highChart,drugsArr[i]);
    }
  },


  buildBanners:function( highChart, drugStr ){
    // space is made for banners by offsetting the xAxis
    // find the xAxis line and position banners relative to it
    var xAxisYpos = highChart.chartHeight - ( highChart.xAxis[0].bottom - highChart.xAxis[0].offset);

    var yOffset = ( (highHelp.drugBanners.length + 1) * 40) + 10;
    var padding = 5;
    var x = padding;
    var y = xAxisYpos - yOffset;
    var h = 35;
    var w = highChart.chartWidth - (padding * 2 );

    // rect (x,y,w,h,radius,border)
    var banner = {};

    // create box for drugs
    banner.box = highChart.renderer.rect( x, y, w, h)
      .addClass('oes-banner-bg')
      .add();

    // create label for drugs
    banner.label = highChart.renderer.text(drugStr, x + 4, y + 14 )
      .addClass('oes-banner-title')
      .add();

    highHelp.drugBanners.push(banner);
  }

};