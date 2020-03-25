<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-IOP.js')?>"></script>

<div id="js-hs-chart-IOP" class="highchart-area" data-highcharts-chart="1" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
    <div id="plotly-IOP-right" class="plotly-IOP plotly-right plotly-section" data-eye-side="right"></div>
    <div id="plotly-IOP-left" class="plotly-IOP plotly-left plotly-section" data-eye-side="left" style="display: none;"></div>
</div>
<script type="text/javascript">

		let readings = {};
		let graph_data = [];

  $(document).ready(function () {
	let IOP_target = <?= CJavaScript::encode($this->getTargetIOP()); ?>;
    let opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
    let laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;
    let sides = ['left', 'right'];
	//plotly
    let iop_plotly_data = <?= CJavaScript::encode($this->getPlotlyIOPData()); ?>;

		for (let side of sides) {
			let layout_iop = JSON.parse(JSON.stringify(layout_plotly));
			layout_iop['shapes'] = [];
			layout_iop['annotations'] = [];
			layout_iop['yaxis'] = setYAxis_IOP();
			layout_iop['height'] = 400;

			setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, opnote_marking, side, 0, 70);
			setMarkingEvents_plotly(layout_iop, marker_line_plotly_options, marking_annotations, laser_marking, side, 0, 70);
            if(iop_plotly_data[side]){
                var x_data = iop_plotly_data[side].map(function (item) {
                    return new Date(item['timestamp']);
                });
                if(IOP_target[side]>0){
                    setYTargetLine(layout_iop, marker_line_plotly_options, marking_annotations, IOP_target, side, x_data[0], x_data[x_data.length - 1]);
                }
            }
            
			let readings = {};

			//Get readings from date
			for (let data_point of iop_plotly_data[side]) {
				let timestamp = data_point['timestamp'];
				if(!readings.hasOwnProperty(timestamp)) {
					readings[timestamp] = [];
				}
				readings[timestamp].push(
				    {
								'id': data_point['id'],
								'event_type': data_point['event_type'],
								'reading': data_point['reading'],
								'comment': data_point['comment'],
					});
			}

			let graph_data = [];

			//Format readings for graph display
			for (let key in readings) {
        let hasExam = readings[key].some(et => et['event_type'].toLowerCase() === 'examination');
        let hasPhasing = readings[key].some(et => et['event_type'].toLowerCase() === 'phasing');

        let eventTypeString = 'No event type found';

        if(hasExam && hasPhasing) {
					eventTypeString = 'Examination & Phasing';
        }else if(hasExam) {
					eventTypeString = 'Examination';
        }else if(hasPhasing) {
					eventTypeString = 'Phasing';
        }


		const unique = (value, index, self) => {
			return self.indexOf(value) === index
		}
		graph_data[key] = {
			'parent_ids': readings[key].map(r => r['id']),
			'timestamp': key,
			'event_type': eventTypeString,
			'minimum': Math.min(...readings[key].map(r => r['reading'])),
			'average': readings[key].map(r => r['reading']).reduce((a, b) => parseInt(a) + parseInt(b), 0) / readings[key].length,
			'maximum': Math.max(...readings[key].map(r => r['reading'])),
			'reading_count': readings[key].length,
			'comment': readings[key].map(r => r['comment']).filter(unique)
		};
      }

		//Create arrays to pass to graph
		let x = [];
		let y = [];
		let event_ids = [];
		let error_array = [];
		let error_minus = [];
		let display_data = [];

		let i = 0;
		for(key in graph_data) {
          x[i] = new Date(+graph_data[key]['timestamp']);
          y[i] = graph_data[key]['average'];
          event_ids[i] = graph_data[key]['parent_ids'];
          error_array[i] = graph_data[key]['maximum'] - graph_data[key]['average'];
          error_minus[i] = graph_data[key]['average'] - graph_data[key]['minimum'];
          display_data[i] =	graph_data[key]['event_type'] + ' IOP';
          if(graph_data[key]['reading_count'] > 1) {
              display_data[i] += '<br>'
              + 'Maximum: ' + Math.round(graph_data[key]['maximum']).toString() + ' mmHg <br>'
              + 'Average: ' + Math.round(graph_data[key]['average']).toString() + ' mmHg <br>'
              + 'Minimum: ' + Math.round(graph_data[key]['minimum']).toString() + ' mmHg <br>'
			  + 'Readings: ' + graph_data[key]['reading_count'].toString();

			  display_data[i] += '<br>Comment: ' + graph_data[key]['comment'];

          }else if(graph_data[key]['reading_count'] === 1){
						display_data[i] += '<br>Reading: ' + Math.round(graph_data[key]['average']).toString() + ' mmHg';
						display_data[i] += '<br>Comment: ' + graph_data[key]['comment'];
          }
          i++;
      }
        let data = [{
				name: 'IOP(' + ((side == 'right') ? 'R' : 'L') + ')',
				x: x,
				y: y,
				line: {
					color: (side == 'right') ? '#9fec6d' : '#fe6767',
				},
				text: x.map(function (item, index) {
          return item.getDate()+'/'+(item.getMonth()+1) +"/"+ item.getFullYear().toString().substring(2)+ '<br>' + display_data[index];
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
		listenForClickEvent('plotly-IOP-right','right');
		listenForClickEvent('plotly-IOP-left','left');

		//Click event for drillthrough data display
		function listenForClickEvent(elementId, side){
			let report = document.getElementById(elementId);
			report.on('plotly_click',function(data){
				for(let i=0; i < data.points.length; i++){
					if (data.points[i].customdata){
						$('.analytics-patient-list').show();
						$('#js-back-to-chart').show();
						$('#oescape-layout').hide();

						$('.event_rows').hide();
						let showlist = data.points[i].customdata;
						for (let j=0; j<showlist.length; j++){
							let id = showlist[j].toString();
							DisplayDrillThroughData(id, side);
						}

						//calculate max displayed value
						iop_plotly_data = <?= CJavaScript::encode(OphCiExamination_Episode_IOPHistory::getDrillthroughIOPDataForEvent($this->patient)); ?>;
						max_visible = 0;
						max_id = '';
						if (showlist.length > 1){
							for (let i = 0; i < iop_plotly_data.length; i++){
								if (showlist.includes(iop_plotly_data[i]["event_id"]))
								{
									if(iop_plotly_data[i]["eye"]===side){
										if (max_visible < parseInt(iop_plotly_data[i]["raw_value"])){
											max_visible = parseInt(iop_plotly_data[i]["raw_value"]);
											max_id = iop_plotly_data[i]["event_id"];
										}
									}
								}
							}
							let rows = $('.event_'+max_id+'_'+side+'.val_'+max_visible);
							let msg = 'Peak IOP '+side.toUpperCase()+' eye'
							rows.css('font-weight','bold');
							rows.children('td').css('color','white');
							let comment = rows.children('.event_comments:not(:contains('+msg+'))');
							if(comment.length>0){
								if(comment[0].innerText)
								comment.append(' - '+msg);
								else
								comment.append(msg);
							}
						}
					}
				}

			});
		}
  });
</script>
