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
<div class="report curvybox white">
	<div class="admin">
		<h3 class="georgia">Users</h3>
		<div class="pagination">
			<?php echo $this->renderPartial('_pagination',array(
				'prefix' => '/admin/users/',
				'page' => $users['page'],
				'pages' => $users['pages'],
			))?>
		</div>
		<div class="search">
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
				'id' => 'searchform',
				'enableAjaxValidation' => false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus' => '#search',
				'action' => Yii::app()->createUrl('/admin/users'),
			))?>
				<span>Search:</span>
				<input type="text" name="search" id="search" value="<?php echo strip_tags(@$_POST['search'])?>" />
			<?php $this->endWidget()?>
		</div>
		<div>
			<form id="admin_users">
				<ul class="grid reduceheight">
					<li class="header">
						<?php /*
						<span class="column_checkbox"><input type="checkbox" name="selectall" id="selectall" /></span>
						*/?>
						<span class="column_id">ID</span>
						<span class="column_username">Username</span>
						<span class="column_title">Title</span>
						<span class="column_firstname">First name</span>
						<span class="column_lastname">Last name</span>
						<span class="column_role">Access level</span>
						<span class="column_doctor">Doctor</span>
						<span class="column_active">Active</span>
					</li>
					<div class="sortable">
						<?php
						foreach ($users['items'] as $i => $user) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $user->id?>">
								<?php /*
								<span class="column_checkbox"><input type="checkbox" name="users[]" value="<?php echo $user->id?>" /></span>
								*/?>
								<span class="column_id"><?php echo $user->id?></span>
								<span class="column_username"><?php echo strtolower($user->username)?></span>
								<span class="column_title"><?php echo $user->title?>&nbsp;</span>
								<span class="column_firstname"><?php echo $user->first_name?></span>
								<span class="column_lastname"><?php echo $user->last_name?></span>
								<span class="column_role"><?php echo $user->accesslevelstring?>&nbsp;</span>
								<span class="column_doctor"><?php echo $user->is_doctor ? 'Yes' : 'No'?></span>
								<span class="column_active"><?php echo $user->active ? 'Yes' : 'No'?></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete', array('colour' => 'blue'))->toHtml()?>
</div>
