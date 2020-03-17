<?php
/**
 * @var $variable_data array
 * @var $variables CaseSearchVariable[]
 */
if ($variable_data) {
    $x = array_column($variable_data[$variable], $variables[0]->field_name);
    $y = array_column($variable_data[$variable], 'frequency');
    $n = $total_patients
?>
<!-- BEGIN PLOT CONTAINER -->
<div id="idgPlot" style="height: calc(100vh - 220px); margin:10px 0" class="js-plotly-plot"></div>
<script type="text/javascript">
    let container = document.getElementsByClassName('js-plotly-plot')[0];
    let data = [
        {
            x: <?= json_encode($x) ?>,
            y: <?= json_encode($y) ?>,
            type: 'bar',
            hovertemplate: '<?= $variables[0]->label ?>: %{x}<br>(N: %{y})',
            name:""
        }
    ];

    // layout
    const layout = oePlotly.getLayout({
        theme: ($('link[data-theme="light"]').prop('media') === 'none') ? 'dark' : 'light',
        plotTitle: '<?= $variables[0]->label ?> distribution N = <?= $total_patients?>',
        legend: false,
        titleX: '<?= $variables[0]->label ?><?= $variables[0]->unit ? " ({$variables[0]->unit})" : ''?>',
        titleY: false,
        numTicksX: <?= count($x) ?>,
        numTicksY: 20,
    });
    Plotly.newPlot(container, data, layout, {displayModeBar: false, responsive: true});
</script>
<!-- END PLOT CONTAINER -->
<?php } ?>