<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/highchart-IOP.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-IOP.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oescape-plotly.js')?>"></script>


<div id="js-hs-chart-IOP" class="highchart-area" data-highcharts-chart="1" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="highcharts-IOP-right" class="highcharts-IOP highcharts-right highchart-section"></div>
    <div id="highcharts-IOP-left" class="highcharts-IOP highcharts-left highchart-section" style="display: none;"></div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    var IOP_data = <?= CJavaScript::encode($this->getIOPData()); ?>;
    var IOP_target = <?= CJavaScript::encode($this->getTargetIOP()); ?>;
    var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;
    var sides = ['left', 'right'];

    //plotly
    var iop_plotly_data = <?= CJavaScript::encode($this->getPlotlyIOPData()); ?>;

    for (var side of sides){
      var x_data = iop_plotly_data[side]['x'].map(function (item) {
        return new Date(item);
      });
      layout_plotly['shapes'] = [];
      layout_plotly['annotations'] = [];
      layout_plotly['yaxis'] = setYAxis_IOP();

      setMarkingEvents_plotly(layout_plotly, marker_line_plotly_options, marking_annotations, opnote_marking, side, 0, 70);
      setMarkingEvents_plotly(layout_plotly, marker_line_plotly_options, marking_annotations, laser_marking, side, 0, 70);

      setYTargetLine(layout_plotly, marker_line_plotly_options, marking_annotations, IOP_target, side, x_data[0], x_data[x_data.length - 1]);
      var data =[{
        name: 'IOP('+((side=='right')?'R':'L')+')',
        x: x_data,
        y: iop_plotly_data[side]['y'],
        line: {
          color: (side=='right')?'#9fec6d':'#fe6767',
        },
        text: iop_plotly_data[side]['x'].map(function (item, index) {
          return  new Date(item) +'<br>IOP('+ side + '): ' +  iop_plotly_data[side]['y'][index];
        }),
        hoverinfo: 'text',
        type: 'line',
      }];

      Plotly.newPlot(
        'highcharts-IOP-'+side, data, layout_plotly, options_plotly
      );
    }

  });
</script>
