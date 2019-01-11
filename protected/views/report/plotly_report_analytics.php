<style>
  .js-plotly-plot .plotly .modebar{
    right: 20px;
  }
</style>
<div id="<?=$report->graphId();?>_container" class="report-container">
  <?php if (method_exists($report, 'renderSearch')):?>
    <?= $report->renderSearch(true); ?>
  <?php else: ?>
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
      <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
      <input type="hidden" name="template" value="<?= $report->getApp()->getRequest()->getQuery('template'); ?>" />
    </form>
  <?php endif;?>
  <div id="<?=$report->graphId();?>" class="chart-container"></div>
</div>
<script>
    var layout = JSON.parse('<?= $report->plotlyConfig();?>');
    layout['font'] = {
            color: '#fff'
        };
    layout['paper_bgcolor'] = '#141e2b';
    layout['plot_bgcolor'] = '#141e2b';
    Plotly.newPlot('<?=$report->graphId();?>',
    <?= $report->tracesJson();?>,
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
  <?php if($report->graphId() !== 'PcrRiskReport'){?>
  var report  = document.getElementById('<?=$report->graphId()?>');
  report.on('plotly_click',function(data){
          for(var i=0; i < data.points.length; i++){
              if (data.points[i].customdata){
                  $('.analytics-event-list').show();
                  $('.analytics-event-list-row').hide();
                  var showlist = data.points[i].customdata;
                  for (var j=0; j<showlist.length; j++){
                      var id = showlist[j].toString();
                      $('#'+id).show();
                  }
              }
          }
  });
  <?php }?>
  var inner =  $('#search-form-report-search-section');
  var search_form = $('#search-form-to-side-bar').html();
  inner.html("");
  inner.html(search_form);
  $('#search-form-to-side-bar').html("");
</script>

