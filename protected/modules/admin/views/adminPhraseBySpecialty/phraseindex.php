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
	$specialtyName
);
$this->menu=array(
	array('label'=>'Create a phrase in section ' . $sectionName . ' for ' . $specialtyName . ' specialty', 'url'=> array('create', 'section_id'=>$sectionId, 'specialty_id'=>$specialtyId)),
	array('label'=>'Manage phrases in this section', 'url'=>array('admin', 'section_id'=>$sectionId)),
);
?>

<h1>Phrase By Specialties</h1>
<h2>Phrases for the section: <?php echo $sectionName; ?> and the specialty: <?php echo $specialtyName; ?></h2>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view'
)); ?>
