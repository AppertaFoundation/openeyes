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
?>
<?php
$uri = preg_replace('/^\//','',preg_replace('/\/$/','',$_SERVER['REQUEST_URI']));

if (!Yii::app()->user->isGuest) {
	$user = User::model()->findByPk(Yii::app()->user->id);
	if (!preg_match('/^profile\//',$uri)) {
		if (!$user->has_selected_firms && !$user->global_firm_rights && empty(Yii::app()->session['shown_reminder'])) {
			Yii::app()->session['shown_reminder'] = true;
			$this->widget('SiteAndFirmWidgetReminder');
		} else if (!empty(Yii::app()->session['confirm_site_and_firm'])) {
			$this->widget('SiteAndFirmWidget', array(
				'returnUrl' => Yii::app()->request->requestUri,
				)
			);
		}
	}
	if (empty(Yii::app()->session['user'])) {
		Yii::app()->session['user'] = User::model()->findByPk(Yii::app()->user->id);
	}
	$user = Yii::app()->session['user'];
	$menu = array();
	foreach (Yii::app()->params['menu_bar_items'] as $menu_item) {
		if (isset($menu_item['restricted'])) {
			$allowed = false;
			foreach ($menu_item['restricted'] as $authitem) {
				if (Yii::app()->user->checkAccess($authitem)) {
					$allowed = true;
					break;
				}
			}
			if (!$allowed) {
				continue;
			}
		}
		$menu[$menu_item['position']] = $menu_item;
	}
	ksort($menu);
	?>

	<div class="panel user">
		<ul class="inline-list navigation user right">
			<?php foreach ($menu as $item) {?>
				<?php if ($uri == $item['uri']) {?>
					<li class="selected">
				<?php } else { ?>
					<li>
				<?php }?>
				<?php echo CHtml::link($item['title'], Yii::app()->getBaseUrl() . '/' . ltrim($item['uri'], '/'))?>
				</li>
			<?php }?>
		</ul>
		<div class="row">
			<div class="large-3 column">
				<div class="user-id">
					You are logged in as:
					<div class="user-name">
						<?php if (Yii::app()->params['profile_user_can_edit']) {?>
							<a href="<?php echo Yii::app()->createUrl('/profile');?>">
								<span class="icon-user-panel-cog"></span>
								<strong><?php echo $user->first_name.' '.$user->last_name;?></strong>
							</a>
						<?php } else {?>
							<strong><?php echo $user->first_name?> <?php echo $user->last_name?></strong>
						<?php }?>
					</div>
				</div>
			</div>
			<div class="large-9 column">
				<div class="user-firm text-right">
					Site: <strong><?php echo Site::model()->findByPk($this->selectedSiteId)->short_name; ?></strong>,
					Firm: <strong><?php echo Firm::model()->findByPk($this->selectedFirmId)->getNameAndSubspecialty(); ?></strong>
					<span class="change-firm">(<a href="#">Change</a>)</span>
				</div>
			</div>
		</div>
	</div>
<?php } ?>