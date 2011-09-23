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
        'Phrase By Specialties' => array('/admin/adminPhraseBySpecialty/index'),
        $sectionName => array('specialtyIndex', 'section_id'=>$sectionId),
        $specialtyName => array('phraseIndex', 'section_id'=>$sectionId, 'specialty_id'=>$specialtyId),
	'Create'
);

$this->menu=array(
	array('label'=>'List phrases by specialty', 'url'=>array('index')),
	array('label'=>'Manage phrases by specialty', 'url'=>array('admin')),
);
?>

<h1>Create PhraseBySpecialty</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
