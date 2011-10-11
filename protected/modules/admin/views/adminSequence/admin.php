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
	'Manage',
);

$this->menu=array(
	array('label'=>'List Sequence', 'url'=>array('index')),
	array('label'=>'Create Sequence', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sequence-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Sequences</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'sequence-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>'Firm',
			'value'=>'$data->getFirmName()',
			'filter'=>CHtml::dropDownList('Firm[id]', $model->firm_id, Firm::model()->getListWithSpecialties(), array('empty' => '')),
		),
		array(
			'header'=>'Site/Theatre',
			'value'=>'$data->theatre->site->name . "-" . $data->theatre->name',
			'filter'=>CHtml::dropDownList('Site[id]', $model->site_id, Site::model()->getList(), array('empty' => '')),
		),
		'start_date',
		array(
			'header'=>'Day',
			'name'=>'weekday',
			'value'=>'date("l ", strtotime($data->start_date))',
			'filter'=>CHtml::dropDownList('Sequence[weekday]', $model->weekday, Sequence::model()->getWeekdayOptions(), array('empty' => '')),
		),
		array(
			'header'=>'Start Time',
			'name'=>'start_time'
		),
		array(
			'header'=>'End Time',
			'name'=>'end_time'
		),
		'end_date',
		array(
			'name'=>'repeat',
			'header'=>'Occurrence',
			'value'=>'$data->getRepeatText()',
			'filter'=>CHtml::dropDownList('Sequence[repeat]', $model->getSelectedFrequencyWeekOption(), Sequence::model()->getFrequencyAndWeekOptions(), array('empty' => '')),
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
