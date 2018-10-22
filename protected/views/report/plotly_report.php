
<div id="<?=$report->graphId();?>_container" class="report-container">
  <?php if (method_exists($report, 'renderSearch')):?>
    <i class="mdl-color-text--blue-grey-400 material-icons search-icon" role="presentation">search</i>
    <?= $report->renderSearch(); ?>
  <?php else: ?>
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
      <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
    </form>
  <?php endif;?>
  <div id="<?=$report->graphId();?>" class="chart-container"></div>
</div>
<script>
	console.log(<?= $report->tracesJson();?>);
	Plotly.newPlot('<?=$report->graphId();?>',
    <?= $report->tracesJson();?>,
    JSON.parse('<?= $report->plotlyConfig();?>'),
		{displayModeBar: false}
  );
</script>