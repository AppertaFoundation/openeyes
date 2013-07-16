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
		<h3 class="georgia">Edit institution</h3>
		<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
		<div>
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'adminform',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus'=>'#username'
			))?>
			<?php echo $form->textField($institution,'name',array('size'=>'50'))?>
			<?php echo $form->textField($institution,'remote_id',array('size'=>'10'))?>
			<?php echo get_class($address)?>
			<?php echo $form->textField($address,'address1')?>
			<?php echo $form->textField($address,'address2')?>
			<?php echo $form->textField($address,'city')?>
			<?php echo $form->textField($address,'county')?>
			<?php echo $form->textField($address,'postcode')?>
			<?php echo $form->dropDownList($address,'country_id',CHtml::listData(Country::model()->findAll(array('order'=>'name')),'id','name'))?>
			<?php $this->endWidget()?>
		</div>
	</div>
</div>
<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Save', 'save', array('colour' => 'green'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('colour' => 'red'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<div class="curvybox white contactLocations">
	<div class="admin">
		<h3 class="georgia">Sites</h3>
		<form id="admin_institution_sites">
			<ul class="grid reduceheight">
				<li class="header">
					<span class="column_id">ID</span>
					<span class="column_remote_id">Remote ID</span>
					<span class="column_name">Name</span>
					<span class="column_address">Address</span>
				</li>
				<?php
				$criteria = new CDbCriteria;
				$criteria->compare('institution_id',$institution->id);
				$criteria->order = 'name asc';
				foreach (Site::model()->findAll($criteria) as $i => $site) {?>
					<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $site->id?>">
						<span class="column_id"><?php echo $site->id?></span>
						<span class="column_remote_id"><?php echo $site->remote_id?>&nbsp;</span>
						<span class="column_name"><?php echo $site->name?>&nbsp;</span>
						<span class="column_address"><?php echo $site->getLetterAddress(array('delimiter'=>', '))?>&nbsp;</span>
					</li>
				<?php }?>
			</ul>
		</form>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/institutions';
	});
	$('li.even,li.odd').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editsite?site_id='+$(this).attr('data-attr-id');
	});
</script>
