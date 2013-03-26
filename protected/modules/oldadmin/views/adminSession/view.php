<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$this->breadcrumbs=array(
	'Sessions'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Sessions', 'url'=>array('index')),
	array('label'=>'Generate Sessions', 'url'=>array('massCreate')),
	array('label'=>'Update Session', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Manage Sessions', 'url'=>array('admin')),
);
?>

<h1>View Session #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'sequence_id',
		'TheatreName',
		'FirmName',
		'date',
		array(
			'name' => 'date',
			'value' => $model->NHSDate('date'),
		),
		array(
			'name' => 'start_time',
			'value' => date('H:i',strtotime($model->start_time))
		),
		array(
			'name' => 'end_time',
			'value' => date('H:i',strtotime($model->end_time))
		),
		array(
			'name' => 'status',
			'value' => $model->getStatusText(),
		),
		'comments',
		array(
			'name' => 'anaesthetist',
			'value' => ($model->anaesthetist) ? 'Yes' : 'No',
		),
		array(
			'name' => 'general_anaesthetic',
			'value' => ($model->general_anaesthetic) ? 'Yes' : 'No',
		),
		array(
			'name' => 'consultant',
			'value' => ($model->consultant) ? 'Yes' : 'No',
		),
		array(
			'name' => 'paediatric',
			'value' => ($model->paediatric) ? 'Yes' : 'No',
		),
		'bookingCount',
	),
)); ?>
