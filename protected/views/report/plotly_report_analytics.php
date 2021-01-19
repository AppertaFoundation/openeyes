<style>
  .js-plotly-plot .plotly .modebar{
    right: 20px;
  }
</style>
<div class="flex-layout">
    <h3><?= $report->getReportTitle() ?></h3>
</div>
<div id="<?=$report->graphId();?>_container" class="report-container">
  <?php if (method_exists($report, 'renderSearch')) :?>
        <?= $report->renderSearch(true); ?>
    <?php else : ?>
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
      <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
      <input type="hidden" name="template" value="<?= $report->getApp()->getRequest()->getQuery('template'); ?>" />
    </form>
    <?php endif;?>
  <div id="<?=$report->graphId();?>" class="chart-container"></div>
</div>
<script>
    var data =  <?= $report->tracesJson();?>;
    let theme = ($('link[data-theme="light"]').prop('media') === 'none') ? 'dark' : 'light';
    let layout = JSON.parse('<?= $report->plotlyConfig();?>');
    const plotly_min_width = 800;
    const plotly_min_height = 650;

    layout['width'] = layout_width;
    layout['height'] = layout_height;
    // If layout for themeable plots exists in the object
    if ("oePlotly" in layout) {
        let oePlotlyLayout = oePlotly_v1.getLayout({
            theme: theme,
            ...layout['oePlotly']
        });
        layout = {...layout, ...oePlotlyLayout}
    } else {
        // Create a layout for non-themeable plots
        layout['font'] = {
                color: '#fff'
            };
        layout['paper_bgcolor'] = '#101925';
        layout['plot_bgcolor'] = '#101925';
        layout['xaxis']['mirror'] = true;
        layout['xaxis']['rangemode'] = 'tozero';
        layout['xaxis']['linecolor'] = '#fff';
        layout['yaxis']['linecolor'] = '#fff';
        layout['yaxis']['automargin'] = true;
        layout['yaxis']['mirror'] = true;
        if (layout['yaxis']['showgrid']){
            layout['yaxis']['gridcolor'] = '#aaa';
        }
        if (layout['xaxis']['showgrid']){
            layout['xaxis']['gridcolor'] = '#aaa';
        }
        <?php if (($report->graphId() === 'PcrRiskReport')) {?>
            layout['shapes'][0]['line']['color'] = '#fff';
        <?php }?>
    }
    Plotly.newPlot('<?=$report->graphId();?>',
        data,
        layout,
    {
      modeBarButtonsToRemove: ['sendDataToCloud','zoom2d', 'pan', 'pan2d',
        'autoScale2d', 'select2d', 'lasso2d', 'zoomIn2d', 'zoomOut2d',
        'orbitRotation', 'tableRotation', 'toggleSpikelines',
        'resetScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],
      responsive: true,
      displaylogo: false,
    }
  );
    <?php if ($report->graphId() !== 'PcrRiskReport') {?>
  var report  = document.getElementById('<?=$report->graphId()?>');
  report.on('plotly_click',function(data){
          for(var i=0; i < data.points.length; i++){
              if (data.points[i].customdata){
                  $('.analytics-charts').hide();
                  $('.analytics-event-list').show();
                  $('.analytics-event-list-row').hide();
                  $('#js-back-to-chart').show();
                  var showlist = data.points[i].customdata;
                  for (var j=0; j<showlist.length; j++){
                      var id = showlist[j].toString();
                      $('#'+id).show();
                  }
              }
          }
  });
    <?php }?>
  // get current search form in sidebar
  var side_bar_inner_filter = $('#search-form-report-search-section');
  // get the hidden form on the plot
  var search_form = $('#search-form-to-side-bar').html() ? $('#search-form-to-side-bar').html() : '';
  // to get plot wrapper which is under div#xxx-xxx-grid
  var report_ctn = $('#<?=$report->graphId();?>_container').parent().attr('id');
  side_bar_inner_filter.html("");
  // try to get the saved search form. if there is none, get new one
  var saved_search_form = analytics_dataCenter.cataract.getCataractSearchForm()['#' + report_ctn];
  if(saved_search_form){
    search_form = saved_search_form.html();
  }
  side_bar_inner_filter.html(search_form);
  $('#search-form-to-side-bar').html("");

    <?php
    if ($report->graphId() === 'OEModule_OphCiExamination_components_RefractiveOutcomeReport') {?>
    $('#refractive-outcome-proc-all').change(function(){
        if (this.checked){
            $(".refractive_outcome_specific_procedure").removeAttr("checked");
        }
    });
    $('.refractive_outcome_specific_procedure').change(function(){
        if (this.checked){
            $("#refractive-outcome-proc-all").removeAttr("checked");
        }
    });
    <?php }?>
</script>

