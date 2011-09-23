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
	'Phrase By Firm'=>array('index'),
	$model->section->name => array('phraseindex', 'section_id'=>$model->section->id),
	$model->name->name,
);

$this->menu=array(
	array('label'=>'Update this phrase', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete this phrase', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'List all phrases in this section', 'url'=>array('phraseindex', 'section_id'=>$model->section->id)),
	array('label'=>'Create new phrase in this section', 'url'=>array('create', 'section_id'=>$model->section->id)),
);
?>

<h1>View PhraseByFirm #<?php echo $model->id; ?></h1>
<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array('name' => 'phrase_name_id', 'value' => $model->name->name),
		'phrase',
		array('name' => 'section_by_firm_id', 'value' => $model->section->name),
		'display_order',
		array('name' => 'firm_id', 'value' => $model->firm->name),
	),
)); ?>
