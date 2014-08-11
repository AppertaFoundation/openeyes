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
<div class="box admin">
	<h2>Edit contact</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#contactname',
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5
		)
	))?>
		<?php echo $form->textField($contact,'title', array('autocomplete'=>Yii::app()->params['html_autocomplete']), null, array('field' => 2))?>
		<?php echo $form->textField($contact,'first_name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'last_name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'nick_name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'primary_phone',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->textField($contact,'qualifications',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->dropDownList($contact,'contact_label_id',CHtml::listData(ContactLabel::model()->active()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
		<?php echo $form->formActions(array('cancel-uri' => '/admin/contacts'))?>
	<?php $this->endWidget()?>
</div>

<div class="box admin">
	<h2>Locations</h2>
	<form id="admin_contact_locations">
		<table class="grid">
			<thead>
				<tr>
					<th>Type</th>
					<th>Name</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($contact->locations as $i => $location) {?>
					<tr class="clickable" data-id="<?php echo $location->id?>" data-uri="admin/contactLocation?location_id=<?php echo $location->id?>">
						<td><?php echo $location->site_id ? 'Site' : 'Institution'?></td>
						<td><?php echo $location->site_id ? $location->site->name : $location->institution->name?>&nbsp;</td>
						<td><a href="#" class="removeLocation" rel="<?php echo $location->id?>">Remove</a></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="3">
						<?php echo EventAction::button('Add', 'add_contact_location', null, array('class' => 'small'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
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
					new OpenEyes.UI.Dialog.Alert({
						content: "This contact location is currently assocated with one or more patients and so cannot be removed.\n\nYou can click on the location row to view the patients involved."
					}).open();
				} else if (resp == "-1") {
					new OpenEyes.UI.Dialog.Alert({
						content: "There was an unexpected error trying to remove the location, please try again or contact support for assistance."
					}).open();
				} else {
					row.remove();
				}
			}
		});
	});

	$('a.removeLocation').click(function(e) {
		return false;
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
