function setYAxis_IOP() {
  return {
    side: 'right',
    title: {
              text: 'IOP Graph',
              font: {
                color: 'white'
              }
            },
    range: [0,75],
    /* Grid line settings of yaxis */
    showgrid: true,
    gridwidth: 0.25,
    gridcolor: '#444',

    /*Ticks setting of yaxis*/
    ticks: 'outside',
    // set y tick white
    tickfont: {
      color: '#fff',
    },
    showticklabels: true,
    dtick: 5,
  };
}

function setYTargetLine(layout, option, annotation, target, side, x_start, x_end){
  var current_marker_line = JSON.parse(JSON.stringify(option));
  current_marker_line['x0'] = x_start;
  current_marker_line['x1'] = x_end;
  current_marker_line['y0'] = target[side];
  current_marker_line['y1'] = target[side];
  current_marker_line['line']['color'] = (side === 'right') ? '#9fec6d' : '#fe6767';
  current_marker_line['line']['width'] = 0.5;
  current_marker_line['line']['dash'] = 'dash';
  layout['shapes'].push(current_marker_line);


  var current_annotation = JSON.parse(JSON.stringify(annotation));
  current_annotation['x']= x_start;
  current_annotation['y']= target[side];
  current_annotation['text']='Target';
  current_annotation['textangle'] = 0;

  layout['annotations'].push(current_annotation);
}