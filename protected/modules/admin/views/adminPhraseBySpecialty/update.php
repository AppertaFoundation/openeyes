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
	'Phrase By Specialties'=>array('index'),
        $model->section->name => array('specialtyIndex', 'section_id'=>$model->section->id),
        $model->specialty->name => array('phraseIndex', 'specialty_id'=>$model->specialty->id, 'section_id'=>$model->section->id),
	$model->name->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PhraseBySpecialty', 'url'=>array('index')),
	array('label'=>'Create PhraseBySpecialty', 'url'=>array('create')),
	array('label'=>'View PhraseBySpecialty', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage PhraseBySpecialty', 'url'=>array('admin')),
);
?>

<h1>Update PhraseBySpecialty <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
