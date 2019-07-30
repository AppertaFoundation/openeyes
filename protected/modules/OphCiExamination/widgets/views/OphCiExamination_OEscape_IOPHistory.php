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

			var readings = {};

			//Get readings from date
			for (var data_point of iop_plotly_data[side]) {
				var timestamp = data_point['timestamp'];
				if(!readings.hasOwnProperty(timestamp)) {
					readings[timestamp] = [];
				}
				readings[timestamp].push(
				    {
								'id': data_point['id'],
								'reading': data_point['reading']
				    });
			}

			var graph_data = [];

			//Format readings for graph display
			for (var key in readings) {
				graph_data[key] = {
				  'parent_ids': readings[key].map(r => r['id']),
					'timestamp': key,
					'minimum': Math.min(...readings[key].map(r => r['reading'])),
					'average': readings[key].map(r => r['reading']).reduce((a, b) => parseInt(a) + parseInt(b), 0) / readings[key].length,
					'maximum': Math.max(...readings[key].map(r => r['reading'])),
					'reading_count': readings[key].length
				};
      }

			//Create arrays to pass to graph
			var x = [];
			var y = [];
			var event_ids = [];
			var error_array = [];
			var error_minus = [];
			var display_data = [];

			var i = 0;
			for(key in graph_data) {
          x[i] = graph_data[key]['timestamp'];
          y[i] = graph_data[key]['average'];
          event_ids[i] = graph_data[key]['parent_ids'];
          error_array[i] = graph_data[key]['maximum'] - graph_data[key]['minimum'];
          error_minus[i] = graph_data[key]['average'] - graph_data[key]['minimum'];
          display_data[i] =
							'Maximum: ' + graph_data[key]['maximum'].toString() + '<br>'
							+ 'Average: ' + graph_data[key]['average'].toString() + '<br>'
							+ 'Minimum: ' + graph_data[key]['minimum'].toString() + '<br>'
							+ 'Readings: ' + graph_data[key]['reading_count'].toString();
          i++;
      }

			var data = [{
				name: 'IOP(' + ((side == 'right') ? 'R' : 'L') + ')',
				x: x,
				y: y,
				line: {
					color: (side == 'right') ? '#9fec6d' : '#fe6767',
				},
				text: x.map(function (item, index) {
				 	var d = new Date(parseInt(item));
          return OEScape.epochToDateStr(d)
							+ '<br>IOP(' + side + '): '
							+ '<br>' + display_data[index];
				}),
				hoverinfo: 'text',
				hoverlabel: trace_hoverlabel,
				type: 'line',
				mode: 'lines+markers',
				marker: {
					symbol: 'circle',
					size: 10,
				},
				customdata: event_ids,
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
		listenForClickEvent('plotly-IOP-right');
		listenForClickEvent('plotly-IOP-left');

		//Click event for drillthrough data display
		function listenForClickEvent(elementId){
			var report = document.getElementById(elementId);
			report.on('plotly_click',function(data){
				for(var i=0; i < data.points.length; i++){
					if (data.points[i].customdata){
						$('.analytics-patient-list').show();
						$('#js-back-to-chart').show();
						$('#oescape-layout').hide();

						$('.event_rows').hide();
						var showlist = data.points[i].customdata;
						for (var j=0; j<showlist.length; j++){
							var id = showlist[j].toString();
							DisplayDrillThroughData(id);
							$('.event_'+id).show();
						}
					}
				}

			});
		}
  });
</script>
