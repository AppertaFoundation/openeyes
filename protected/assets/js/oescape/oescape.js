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

    $('.js-oes-eyeside').removeClass('selected'); //deselect the other buttons
    $(this).addClass('selected'); //select the current button

    switch(side){
        case 'left':
            $('#oes-side-indicator-left').show().appendTo($('#oes-side-indicator')).css("display", "inline-block"); //show the left eye indicator
            $('#oes-side-indicator-right').hide().appendTo($('#oes-side-indicator')); //hide the right eye indicator

            $('#plotly-Meds-left').appendTo($('#js-hs-chart-Meds'));
            $('#plotly-IOP-left').appendTo($('#js-hs-chart-IOP'));
            $('#plotly-VA-left').appendTo($('#js-hs-chart-VA'));
            $('#plotly-MR-left').appendTo($('#js-hs-chart-MR'));
            // fix ordering for IOP under general
            if ($("#charts-container").hasClass('General')){
                $('#plotly-IOP-left').appendTo($('#js-hs-chart-IOP'));
            }
            $('.plotly-left').show(); //show the left eye
            $('.plotly-right').hide(); //hide the right eye
            $('.SelectorPadRight').remove();//reset dropdown space
            $('#right-charts-container').remove(); //reset right hand side charts container

            //enable previous right side content
            $('.oes-right-side > div').not('.plotly-left').show();
            if ($('.oes-right-side').find('.oes-data-row-input')){
                $('#oct_stack_' + side).show();
                $('#oct_stack_' + other_side).hide();
            }
            break;
        case 'right':
            $('#oes-side-indicator-left').hide().appendTo($('#oes-side-indicator')); //show the right eye indicator
            $('#oes-side-indicator-right').show().appendTo($('#oes-side-indicator')); //show the right eye indicator

            // put the left hand content back so that we dont delete it by accident
            $('#plotly-Meds-left').appendTo($('#js-hs-chart-Meds'));
            $('#plotly-IOP-left').appendTo($('#js-hs-chart-IOP'));
            $('#plotly-VA-left').appendTo($('#js-hs-chart-VA'));
            $('#plotly-MR-left').appendTo($('#js-hs-chart-MR'));
            if ($("#charts-container").hasClass('General')){
                $('#plotly-IOP-left').appendTo($('#js-hs-chart-IOP'));
            }

            $('#plotly-Meds-right').appendTo($('#js-hs-chart-Meds'));
            $('#plotly-IOP-right').appendTo($('#js-hs-chart-IOP'));
            $('#plotly-VA-right').appendTo($('#js-hs-chart-VA'));
            $('#plotly-MR-right').appendTo($('#js-hs-chart-MR'));
            // fix ordering for IOP under general
            if ($("#charts-container").hasClass('General')){
                $('#plotly-IOP-left').appendTo($('#js-hs-chart-IOP'));
            }
            $('.plotly-right').show(); //show the right eye
            $('.plotly-left').hide(); //hide the left eye
            $('.SelectorPadRight').remove(); //reset dropdown space
            $('#right-charts-container').remove(); //reset right hand side charts container

            //enable previous right side content
            $('.oes-right-side > div').not('.plotly-left').show();
            if ($('.oes-right-side').find('.oes-data-row-input')){
                $('#oct_stack_' + side).show();
                $('#oct_stack_' + other_side).hide();
            }
            break;

        case 'both':
            $('.SelectorPadRight').remove();//reset dropdown space

            $('.oes-right-side > div').not('.highchart-area').hide();  //disable previous right side content

            if($('#right-charts-container').length === 0) {
                $('<div id="right-charts-container" class="highchart-area General"><div id="oes-right-side-indicator" style=" height:' + $('#oes-side-indicator').height() + 'px; text-align: center;"></div></div></div>').clone().appendTo($('.oes-right-side'));   //add padding for reset zoom button on right
            }

            $('#oes-side-indicator-left').show().appendTo($('#oes-right-side-indicator')).css("display", "inline-block"); //show the left eye indicator
            $('#oes-side-indicator-right').show().appendTo($('#oes-side-indicator')).css("display", "inline-block"); //show the right eye indicator

            $('#plotly-Meds-left , #plotly-IOP-left').appendTo($('#right-charts-container'));

            //add vertical padding to substitute for dropdown selectors on right
            $('<div class="SelectorPadRight" style=" padding:' + $('#va-history-form').height() + 'px 100% 0 0"><div>').clone().appendTo($('#right-charts-container'));
            $('<div class="SelectorPadRight" style=" padding:' + $('#mr-history-form').height() + 'px 100% 0 0"><div>').clone().appendTo($('#right-charts-container'));

            $('#plotly-VA-left').appendTo($('#right-charts-container')); //adding graph content to right side
            $('#plotly-MR-left').appendTo($('#right-charts-container')); //adding graph content to right side

            // fix ordering for IOP under GENERAL OPHTHALMOLOGY
            if ($("#charts-container").hasClass('General')){
                $('#plotly-IOP-left').appendTo($('#right-charts-container'));
            }
            $('.plotly-right, .plotly-left').show(); //show both sides
            $('.oes-right-side').css('padding', '20px 0 20px 0'); // fix right side padding css

            // click the 50/50 split option to apply the default scaling for both as otherwise the right side may be obscured.
            $('.js-oes-area-resize[data-area ="medium"]').click();
            break;

        default:
            break;
    }

    setOEScapeSize();
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
  size_str = size_str || $('.js-oes-area-resize.selected').data('area');
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

  right.css({"width": (100-sizes[size_str].percent)+'%'});

  let doc_width = $(document).width();

  let width_reduction = (doc_width/100); // dont use this 1 percent of the screen width, as this gets rid of most of the bottom scroll bar caused by rounding errors

  let current_width = ((doc_width*sizes[size_str].percent)/100) - width_reduction;
  let left_width = current_width>sizes[size_str].min_width ? current_width: sizes[size_str].min_width;
  let right_width =((doc_width*(100-sizes[size_str].percent))/100) - width_reduction;

  right.css({"width": right_width});
  right.toggle(size_str !== 'full');

  let left_update = {
    width: left_width>0?left_width:1,
  };
  let right_update = {
    width: right_width>0?right_width:1,
  };
  if (eye_side != 'both'){
    var plotly_list_l = $('.plotly-'+eye_side);
  }
  else{
    var plotly_list_l = $('.plotly-right');
    var plotly_list_r = $('.plotly-left');

    for (let i = 0; i < plotly_list_r.length; i++){
      let plotly_id = plotly_list_r[i].id;
      Plotly.relayout(plotly_id, right_update);
    }
  }
  for (let i = 0; i < plotly_list_l.length; i++){
    let plotly_id = plotly_list_l[i].id;
    Plotly.relayout(plotly_id, left_update);
  }
}
