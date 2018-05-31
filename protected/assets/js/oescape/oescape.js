$(document).ready(function () {

  var left = $('.oes-left-side'),
    right = $('.oes-right-side'),
    size, percent;


// setup resize buttons
  // buttons have data-area attribute: small, medium, large and full
  $('.js-oes-area-resize').click(function( e ){
    e.stopPropagation();
    $('.js-oes-area-resize.selected').removeClass('selected');
    $(this).addClass('selected');
    var str = $(this).data('area');
    switch(str){
      case 'small':
        size = 500;
        percent = '30%';
        break;
      case 'medium':
        size = 700;
        percent = '50%';
        break;
      case 'large':
        size = 900;
        percent = '70%';
        break;
      case 'full':
        size = null;  // null, when passed to highcharts makes chart fill container
        break;
    }

    // fullsize requires some tweaking
    if(size == null){
      left.css({"min-width":"500px", "width":"100%"});
      right.hide();
    } else {
      left.css({"min-width": size + "px", "width": percent});
      right.show();
    }
    var highcarts_list = $('.highchart-section');
    for (var i = 0; i<  highcarts_list.length; i++){
      if ($(highcarts_list[i]).is(":visible")){
        $(highcarts_list[i]).highcharts().reflow();
      }
    }
  });

  //switch between right and left eye
  $('.js-oes-eyeside-right').click(function (e) {
    e.preventDefault();
    $(this).addClass('selected');
    $('.js-oes-eyeside-left').removeClass('selected');
    $('.highcharts-right').show();
    $('.highcharts-left').hide();
    $('.highcharts-right').highcharts().redraw();

  });

  $('.js-oes-eyeside-left').click(function (e) {
    e.preventDefault();
    $(this).addClass('selected');
    $('.js-oes-eyeside-right').removeClass('selected');
    $('.highcharts-right').hide();
    $('.highcharts-left').show();
    $('.highcharts-left').highcharts().redraw();
  });

  // exit oescape and go back to last viewed (non-oes) page
  $('#js-exit-oescape').click( function(){
    if(localStorage.getItem("lastPage")){
      window.location = localStorage.getItem("lastPage");
    } else {
      window.location.href = $(this).data('link');
    }
  });

});


function addSeries(chart, title, data, options){
  chart.addSeries(Object.assign({
    name: title,
    data: data,
  }, options));
}

function setYLabels(tick_positions, tick_labels){
  return {
    formatter: function () {
      for (var i=0; i<tick_positions.length; i++)
        if (tick_positions[i] === this.value)
          return tick_labels[i];
      return null;
    }
  }
}