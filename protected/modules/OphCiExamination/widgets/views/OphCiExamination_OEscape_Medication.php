<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-Meds.js')?>"></script>

<div id="js-hs-chart-Meds" class="highchart-area" data-highcharts-chart="2" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="plotly-Meds-right" class="plotly-Meds plotly-right plotly-section"></div>
    <div id="plotly-Meds-left" class="plotly-Meds plotly-left plotly-section" style="display: none;"></div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    var meds_data = <?= CJavaScript::encode($this->getMedicationList()); ?>;
    var sides = ['left', 'right'];
    var layout_meds = JSON.parse(JSON.stringify(layout_plotly));
    //plotly
    for (var side of sides){
      var data = [];
      var text_trace = {
        x: [],
        y: [],
        text: [],
        mode: 'text',
        hoverinfo: 'skip',
        textposition: "middle right",
        textfont: {
          color:'#ffffff',
        },
      };

      for (key in meds_data[side]){
        text_trace['x'].push(new Date(meds_data[side][key][0]['low']));
        text_trace['y'].push(key);
        text_trace['text'].push(key);
        for (i in meds_data[side][key]){
          var x_values = [new Date(meds_data[side][key][i]['low']), new Date(meds_data[side][key][i]['high'])];
          var y_values = [key, key];
          var trace = {
            type: 'scatter',
            mode: "lines",
            x: x_values,
            y: y_values,
            hovertext: key+"<br>"+x_values[0].toLocaleDateString()+"-"+x_values[1].toLocaleDateString(),
            hoverinfo: 'text',
            hoverlabel: trace_hoverlabel,
            line: {
              width: 20,
              color: (side=='right')?'#5f8e41':'#983e3e',
            }
          };
          data.push(trace);
        }
      }
      data.push(text_trace);
      layout_meds['yaxis'] = meds_yaxis;
      layout_meds['height'] = 45*Object.keys(meds_data[side]).length+50;
      layout_meds['showlegend'] = false;
      layout_meds['xaxis']['showticklabels'] = false;

      Plotly.newPlot('plotly-Meds-'+side, data, layout_meds, options_plotly);
    }
  });
</script>