<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
$uri = preg_replace('/^\//','',preg_replace('/\/$/','',$_SERVER['REQUEST_URI']));
if (!Yii::app()->user->isGuest) {
	if (empty(Yii::app()->session['user'])) {
		Yii::app()->session['user'] = User::model()->findByPk(Yii::app()->user->id);
	}
	$user = Yii::app()->session['user'];
	$menu = array();
	foreach(Yii::app()->params['menu_bar_items'] as $menu_item) {
		$menu[$menu_item['position']] = $menu_item;
	}
	ksort($menu);
?>
<div id="user_panel">
	<div id="user_nav" class="clearfix">
		<ul>
			<?php foreach($menu as $item) {?>
				<li>
					<?php if ($uri == $item['uri']) {?>
					<span class="selected"><?php echo $item['title']?></span>
					<?php } else { ?>
					<span><?php echo CHtml::link($item['title'],'/'.Yii::app()->baseUrl.$item['uri'])?></span>
					<?php }?>
				</li>
			<?php }?>
		</ul>
	</div>
	<?php if ($this->showForm) { ?>
	<div id="user_firm">
		<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
			'action' => Yii::app()->createUrl('site')
		));
		?>
		<label>Selected firm: </label>
		<?php
		echo CHtml::dropDownList('selected_firm_id', $this->selectedFirmId, $this->firms);
		$this->endWidget();
		?>
	</div>
	<?php } ?>
	<div id="user_id">
		<span>You are logged in as:</span> <strong><?php echo $user->first_name ?> <?php echo $user->last_name; ?></strong>
	</div>
</div>
<?php } ?>
