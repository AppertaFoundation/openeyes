<div id="theatres">
<strong>Select a session time:</strong>
<ul>
<?php
$tabs = array();
foreach ($theatres as $name => $sessions) {
	$tabs[$name] = '';
	foreach ($sessions as $session) {
		$startTime = substr($session['start_time'], 0, 5);
		$endTime = substr($session['end_time'], 0, 5);
		$status = $session['time_available'] >= 0 ? 'available' : 'overbooked';
		$tabs[$name] .= "<button>{$startTime} - {$endTime}<br />
			<span class=\"{$session['status']}\">" . abs($session['time_available']) . " min {$status}</span>
			<span class=\"session_id\">{$session['id']}</span></button>";
	}
}

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
		'select'=>"js:function(event, ui) {
			if ($('#bookings').length > 0) {
				$('#bookings').remove();
			}
		}",
    ),
));
?>
</div>