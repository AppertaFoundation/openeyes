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

$this->breadcrumbs=array(
	'Sequences'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Sequence', 'url'=>array('index')),
	array('label'=>'Create Sequence', 'url'=>array('create')),
	array('label'=>'Update Sequence', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Sequence', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sequence', 'url'=>array('admin')),
);
?>

<h1>View Sequence #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label' => 'Firm',
			'value' => $model->getFirmName()
		),
		array(
			'label' => 'Theatre',
			'value' => $model->theatre->site->name . ' - ' . $model->theatre->name
		),
		array(
			'label' => 'Start Date',
			'value' => $model->start_date . ' (' . date('l', strtotime($model->start_date)) . ')'
		),
		array(
			'label' => 'Start Time',
			'value' => substr($model->start_time, 0, 5)
		),
		array(
			'label' => 'End Time',
			'value' => substr($model->end_time, 0, 5)
		),
		'end_date',
		array(
			'label' => 'Occurrence',
			'value' => !empty($model->week_selection) ? $model->getWeekText() : $model->getFrequencyText(),
		),
	),
)); ?>
