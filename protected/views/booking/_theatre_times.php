<div id="theatres">
<strong>Select a session time:</strong>
<ul>
<?php
$tabs = array();
foreach ($theatres as $name => $sessions) {
	$pretext = '<div id="box_grey_gradient_top"></div>
<div id="box_grey_gradient_bottom">';
	$tabs[$name] = '';
	foreach ($sessions as $session) {
		$startTime = substr($session['start_time'], 0, 5);
		$endTime = substr($session['end_time'], 0, 5);
		$status = $session['time_available'] >= 0 ? 'available' : 'overbooked';
		$prevText = $tabs[$name];
		$tabs[$name] = "<div class=\"shinybutton\"><div>{$startTime} - {$endTime}<br />
			<span class=\"{$session['status']}\">(" . abs($session['time_available']) . " min {$status})</span>
			<span class=\"session_id\">{$session['id']}</span></div></div>" . $prevText;
	}
	$posttext = '</div>';
	
	$tabs[$name] = $pretext . $tabs[$name] . $posttext;
}

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
	'id'=>'theatre-times',
	'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
	'theme'=>'theme',
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>false,
		'select'=>"js:function(event, ui) {
			if ($('#bookings').length > 0) {
				$('#bookings').remove();
			}
		}",
    ),
));
?>
</div>