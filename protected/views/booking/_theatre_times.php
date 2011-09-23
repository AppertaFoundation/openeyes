<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

?>
<div id="theatres">
<strong>Select a session time:</strong>
<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
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
