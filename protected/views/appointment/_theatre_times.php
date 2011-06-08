<div id="theatres">
<strong>Select a session time:</strong>
<ul>
<?php
$tabs = array();
foreach ($theatres as $name => $sessions) {
	foreach ($sessions as $session) {
		$startTime = substr($session['start_time'], 0, 5);
		$endTime = substr($session['end_time'], 0, 5);
		$tabs[$name] = "<button>{$startTime} - {$endTime}<br />
			<span class=\"{$session['status']}\">{$session['time_available']} min available</span>
			<span id=\"session_id\">{$session['id']}</span></button>";
	}
}

$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>true,
    ),
));
?>
</div>