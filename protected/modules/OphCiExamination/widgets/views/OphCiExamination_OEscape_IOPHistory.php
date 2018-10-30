<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-IOP.js')?>"></script>


<div id="js-hs-chart-IOP" class="highchart-area" data-highcharts-chart="1" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="plotly-IOP-right" class="plotly-IOP plotly-right plotly-section" data-eye-side="right"></div>
    <div id="plotly-IOP-left" class="plotly-IOP plotly-left plotly-section" data-eye-side="left" style="display: none;"></div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
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
      var layout_iop = JSON.parse(JSON.stringify(layout_plotly));
      layout_iop['shapes'] = [];
      layout_iop['annotations'] = [];
      layout_iop['yaxis'] = setYAxis_IOP();
      layout_iop['height'] = 400;

      setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, opnote_marking, side, 0, 70);
      setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, laser_marking, side, 0, 70);

      setYTargetLine(layout_iop, marker_line_plotly_options, marking_annotations, IOP_target, side, x_data[0], x_data[x_data.length - 1]);
      var data =[{
        name: 'IOP('+((side=='right')?'R':'L')+')',
        x: x_data,
        y: iop_plotly_data[side]['y'],
        line: {
          color: (side=='right')?'#9fec6d':'#fe6767',
        },
        text: iop_plotly_data[side]['x'].map(function (item, index) {
          var d = new Date(item);
          return OEScape.epochToDateStr(d)+'<br>IOP('+ side + '): ' +  iop_plotly_data[side]['y'][index];
        }),
        hoverinfo: 'text',
        hoverlabel: trace_hoverlabel,
        type: 'line',
        mode: 'lines+markers',
        marker: {
          symbol: 'circle',
          size: 10,
        },
      }];

      Plotly.newPlot(
        'plotly-IOP-'+side, data, layout_iop, options_plotly
      );
    }

  });
</script>
