$(document).ready(function () {

// setup resize buttons
  // buttons have data-area attribute: small, medium, large and full
  $('.js-oes-area-resize').click(function( e ){
    e.stopPropagation();
    $('.js-oes-area-resize.selected').removeClass('selected');
    $(this).addClass('selected');
    var str = $(this).data('area');
    setOEScapeSize(str);
    //Send the preferred size back to the server
    $.ajax({
      'type': 'POST',
      'data': {'chart_size' : str, 'YII_CSRF_TOKEN' : YII_CSRF_TOKEN},
      'url': baseUrl+'/OphCiExamination/OEScapeData/SetPreferredChartSize',
    });
  });


  //switch between right and left eye
  $('.js-oes-eyeside').click(function (e) {
    e.preventDefault();
    var side = $(e.target).attr('data-side');
    var other_side = side === 'left' ? 'right' : 'left';

    $('.js-oes-eyeside').removeClass('selected'); //deselect the other button
    $(this).addClass('selected'); //select the button
    $('.plotly-' + side).show(); //show the new side
    $('.plotly-' + other_side).hide(); //hide the other side

    if ($('.oes-right-side').find('.oes-data-row-input')){
      $('#oct_stack_' + side).show();
      $('#oct_stack_' + other_side).hide();
    }

    setOEScapeSize($('.js-oes-area-resize.selected').data('area'));
  });

  // exit oescape and go back to last viewed (non-oes) page
  $('#js-exit-oescape').click( function(){
      window.location.href = $(this).data('link');
  });

});


var OEScape = {
  toolTipFormatters: {
    VA: function(dataPoint){
      var pos = dataPoint.y;
      var nearestTickIndex = 0;
      var ticks = OEScape.full_va_ticks;
      for (var ii = 0; ii < ticks.tick_position.length; ii++){
        if(ticks.tick_position[ii] <= pos){
          nearestTickIndex = ii;
        } else {
          break;
        }
      }

      return OEScape.toolTipFormatters.EpochSeriesValue(
        dataPoint.x,
        dataPoint.series.name,
        ticks.tick_labels[nearestTickIndex]
      );
    },
    Default: function(dataPoint){
      return this.EpochSeriesValue(dataPoint.x, dataPoint.series.name, dataPoint.y);
    },
    EpochSeriesValue: function(epoch_date, series_name, value){
      return  '<div>' + OEScape.epochToDateStr(epoch_date) +'<br/>' + series_name + ': ' + value;
    },
  },
  toolTipFormatters_plotly: {
    VA: function (x, y, series_name) {
      var nearestTickIndex = 0;
      var ticks = OEScape.full_va_ticks;
      for (var ii = 0; ii < ticks.tick_position.length; ii++){
        if(ticks.tick_position[ii] <= y){
          nearestTickIndex = ii;
        } else {
          break;
        }
      }

      return OEScape.epochToDateStr(x) +'<br>' + series_name + ': ' +  ticks.tick_labels[nearestTickIndex];
    }
  },
  epochToDateStr: function(epoch){
    var date = new Date(epoch);
    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
  },
  full_va_ticks: {'tick_position':[0], 'tick_labels':['error']},
};


//Takes a list (sorted smallest to largest) and removes overlapping labels
function pruneYTicks(ticks, plotHeight, label_height){
  var new_ticks = [];
  new_ticks['tick_position'] = [];
  new_ticks['tick_labels'] = [];
  var plot_min = Math.min.apply(null, ticks.tick_position);
  var plot_max = Math.max.apply(null, ticks.tick_position);
  var high_point = Number.NEGATIVE_INFINITY;
  var plot_per_data = plotHeight / (plot_max - plot_min);

  for( var ii = 0; ii < ticks.tick_position.length; ii++){
    var tick_lower = (ticks.tick_position[ii] - plot_min) * plot_per_data - (label_height / 2);
    if(tick_lower < high_point){
      continue;
    }
    high_point = tick_lower + label_height;

    new_ticks.tick_position.push(ticks.tick_position[ii]);
    new_ticks.tick_labels.push(ticks.tick_labels[ii]);
  }
  return new_ticks;
}


/**
 * Sets the horizontal size of the OEScape graphs
 * @param size_str string (small|medium|large|full)
 */
function setOEScapeSize(size_str){
  var eye_side = $('.js-oes-eyeside.selected').data('side');
  //This refers to the left and right of the screen, not the eyes
  var left = $('.oes-left-side'),
    right = $('.oes-right-side');

  var sizes = {
    'small' : {"min_width":500, 'percent':30},
    'medium': {"min_width":700, 'percent':50},
    'large' : {"min_width":900, 'percent':70},
    'full'  : {"min_width":500, 'percent':100}
  };

  //This needs doing before and after the change in size to prevent mis-alignments between the graphs
  left.css({"min_width":sizes[size_str].min_width, "width":sizes[size_str].percent+'%'});

  var current_width = $(document).width()*sizes[size_str].percent/100;
  var left_width = current_width>sizes[size_str].min_width ? current_width: sizes[size_str].min_width;
  right.css({"width":$(document).width()-left_width});
  right.toggle(size_str !== 'full');
  var update = {
    width: left_width,
  };

  var plotly_list = $('.plotly-'+eye_side);
  for (var i = 0; i < plotly_list.length; i++){
    var plotly_id = plotly_list[i].id;
    Plotly.relayout(plotly_id, update);
  }
}



