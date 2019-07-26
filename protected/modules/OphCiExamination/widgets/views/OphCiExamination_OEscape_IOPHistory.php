<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-IOP.js')?>"></script>

<div id="js-hs-chart-IOP" class="highchart-area" data-highcharts-chart="1" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="plotly-IOP-right" class="plotly-IOP plotly-right plotly-section" data-eye-side="right"></div>
    <div id="plotly-IOP-left" class="plotly-IOP plotly-left plotly-section" data-eye-side="left" style="display: none;"></div>
</div>
<script type="text/javascript">

		var readings = {};
		var graph_data = [];

  $(document).ready(function () {

		var IOP_target = <?= CJavaScript::encode($this->getTargetIOP()); ?>;
    var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;
    var sides = ['left', 'right'];

    var iop_plotly_data = <?= CJavaScript::encode($this->getPlotlyIOPData()); ?>;

    //console.log(iop_plotly_data);

		for (var side of sides) {
			var layout_iop = JSON.parse(JSON.stringify(layout_plotly));
			layout_iop['shapes'] = [];
			layout_iop['annotations'] = [];
			layout_iop['yaxis'] = setYAxis_IOP();
			layout_iop['height'] = 400;

			setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, opnote_marking, side, 0, 70);
			setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, laser_marking, side, 0, 70);

			if (IOP_target[side] > 0) {
				//setYTargetLine(layout_iop, marker_line_plotly_options, marking_annotations, IOP_target, side, x_data[0], x_data[x_data.length - 1]);
			}

			//BEGIN SAMPLE DATA

			var readings = {};

			for (var data_point of iop_plotly_data[side]) {
				//console.log(data_point);
				var timestamp = data_point['timestamp'];
				if(!readings.hasOwnProperty(timestamp)) {
					readings[timestamp] = [];
				}
				readings[timestamp].push(data_point['reading']);
			}

			//console.log(readings);

			var graph_data = [];

			for (var key in readings) {
				//console.log("reading data");
				//console.log(readings[key])
				//console.log(readings[key].reduce((a, b) => parseInt(a) + parseInt(b), 0));
				//console.log(readings[key].length);
				graph_data[key] = {
					'timestamp': key,
					'minimum': Math.min(...readings[key]),
					'average': readings[key].reduce((a, b) => parseInt(a) + parseInt(b), 0) / readings[key].length,
					'maxmimum': Math.max(...readings[key])
				};
      }

			//END SAMPLE DATA

			//console.log(graph_data);

			var x = [];
			var y = [];
			var error_array = [];
			var error_minus = [];

			var i = 0;
			for(key in graph_data) {
          console.log("Max:".concat(graph_data[key]['maximum']));
          console.log("Min:".concat(graph_data[key]['minimum']));

          x[i] = graph_data[key]['timestamp'];
          y[i] = graph_data[key]['average'];
          error_array[i] = graph_data[key]['maximum'] - graph_data[key]['minimum'];
          error_minus[i] = graph_data[key]['average'] - graph_data[key]['minimum'];
          i++;
      }

			console.log("Graphing values:");
			console.log("x:");
			console.log(x);
			console.log("y:");
			console.log(y);
			console.log("error array:");
			console.log(error_array);
			console.log("error minus:");
			console.log(error_minus);

			var data = [{
				name: 'IOP(' + ((side == 'right') ? 'R' : 'L') + ')',
				x: x,
				y: y,

				//x: x_data,
				//y: iop_plotly_data[side]['y'],


				line: {
					color: (side == 'right') ? '#9fec6d' : '#fe6767',
				},
				text: "foobar",
				// text: iop_plotly_data[side]['x'].map(function (item, index) {
				// 	var d = new Date(item);
				// 	return OEScape.epochToDateStr(d) + '<br>IOP(' + side + '): ' + iop_plotly_data[side]['y'][index];
				// }),
				hoverinfo: 'text',
				hoverlabel: trace_hoverlabel,
				type: 'line',
				mode: 'lines+markers',
				marker: {
					symbol: 'circle',
					size: 10,
				},
				error_y: {
					type: "data",
					symmetric: false,
					color: "#888",
					array: error_array,
					arrayminus: error_minus,
					visible: true
				},
			}];

			Plotly.newPlot(
				'plotly-IOP-' + side, data, layout_iop, options_plotly
			);
		}
  });
</script>
