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

$patientName = $model->first_name . ' ' . $model->last_name;
$this->breadcrumbs=array(
	"{$patientName} ({$model->hos_num})",
);
Yii::app()->clientScript->registerCssFile('/css/patient.css', 'screen, projection');
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.fancybox',
	'config'=>array()
	));

?>
<div id="patientHeader">
	<strong>Patient:</strong> <?php echo $patientName;
	echo " ({$model->hos_num})"; ?>
</div>
<?php

if ($tab == 1) {
	$params = array('patient/episodes', 'id'=>$model->id);

	if (isset($eventId)) {
		$params['eventId'] = $eventId;
	}

	$this->widget('zii.widgets.jui.CJuiTabs', array(
	'tabs'=>array(
		'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
		'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id, 'event'=>$event)),
	),
	'id'=>'patient-tabs',
    // additional javascript options for the tabs plugin
    'options'=>array(
        'collapsible'=>false,
		'selected'=>1
	),
));
} else {
	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'tabs'=>array(
			'Summary'=>array('ajax'=>array('patient/summary', 'id'=>$model->id)),
			'View Episodes'=>array('ajax'=>array('patient/episodes', 'id'=>$model->id)),
		),
		'id'=>'patient-tabs',
		'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
		'theme'=>'theme',
		// additional javascript options for the tabs plugin
		'options'=>array(
			'collapsible'=>false,
		),
	));
}
