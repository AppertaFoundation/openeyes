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
<div class="curvybox white">
	<div class="admin">
		<h3 class="georgia">Edit contact</h3>
		<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
		<div>
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'adminform',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus'=>'#contactname'
			))?>
			<?php echo $form->textField($contact,'title')?>
			<?php echo $form->textField($contact,'first_name')?>
			<?php echo $form->textField($contact,'last_name')?>
			<?php echo $form->textField($contact,'nick_name')?>
			<?php echo $form->textField($contact,'primary_phone')?>
			<?php echo $form->textField($contact,'qualifications')?>
			<?php echo $form->dropDownList($contact,'contact_label_id',CHtml::listData(ContactLabel::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
			<?php $this->endWidget()?>
		</div>
	</div>
</div>
<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Save', 'save', array('colour' => 'green'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'contact_cancel', array('colour' => 'red'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<div class="curvybox white contactLocations">
	<div class="admin">
		<h3 class="georgia">Locations</h3>
		<form id="admin_contact_locations">
			<ul class="grid reduceheight">
				<li class="header">
					<span class="column_type">Type</span>
					<span class="column_name">Name</span>
					<span class="column_action">Actions</span>
				</li>
				<?php
				foreach ($contact->locations as $i => $location) {?>
					<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $location->id?>">
						<span class="column_type"><?php echo $location->site_id ? 'Site' : 'Institution'?></span>
						<span class="column_name"><?php echo $location->site_id ? $location->site->name : $location->institution->name?>&nbsp;</span>
						<span class="column_action"><a href="#" class="removeLocation" rel="<?php echo $location->id?>">Remove</a></span>
					</li>
				<?php }?>
			</ul>
		</form>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add_contact_location', array('colour' => 'blue'))->toHtml()?>
</div>
<script type="text/javascript">
	$('a.removeLocation').click(function(e) {
		e.preventDefault();

		var location_id = $(this).attr('rel');

		var row = $(this).parent().parent();

		$.ajax({
			'type': 'POST',
			'data': 'location_id='+location_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'url': baseUrl+'/admin/removeLocation',
			'success': function(resp) {
				if (resp == "0") {
					alert("This contact location is currently assocated with one or more patients and so cannot be removed.\n\nYou can click on the location row to view the patients involved.");
				} else if (resp == "-1") {
					alert("There was an unexpected error trying to remove the location, please try again or contact support for assistance.");
				} else {
					row.remove();
				}
			}
		});
	});

	$('a.removeLocation').click(function(e) {
		return false;
	});

	$('li.even, li.odd').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/contactLocation?location_id='+$(this).attr('data-attr-id');
	});

	handleButton($('#et_add_contact_location'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/addContactLocation?contact_id=<?php echo $contact->id?>';
	});

	handleButton($('#et_contact_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/contacts';
	});
</script>
