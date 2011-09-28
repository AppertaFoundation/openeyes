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

if (!Yii::app()->user->isGuest) {
	if (!empty(Yii::app()->session['user'])) {
		$user = Yii::app()->session['user'];
	} else {
		$user = User::model()->findByPk(Yii::app()->user->id);
		Yii::app()->session['user'] = $user;
	} ?>
<div id="user_info">
You are logged in as: <strong><?php echo $user->first_name . ' ' . $user->last_name; ?></strong>

<?php $this->widget('zii.widgets.CMenu',array(
	'items'=>array(
		array('label'=>'Home', 'url'=>array('/site/index'), 'visible'=>!Yii::app()->user->isGuest),
		array('label'=>'Waiting List', 'url'=>array('/waitingList'), 'visible'=>!Yii::app()->user->isGuest),
		array('label'=>'Diary', 'url'=>array('/theatre'), 'visible'=>!Yii::app()->user->isGuest),
		array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
		array('label'=>'Logout', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
	),
	'id' => 'navlist',
)); ?>
<br />
<?php
if ($this->showForm) {
	echo '<div id="selectedFirm">Selected firm: ';
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'base-form',
		'enableAjaxValidation'=>false,
		'action' => Yii::app()->createUrl('site')
	));
	echo CHtml::dropDownList('selected_firm_id', $this->selectedFirmId, $this->firms);
	$this->endWidget();
	echo '</div>';
}
?>
</div>
<div class="clear"></div>
<?php
}
