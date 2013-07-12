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
<div class="whiteBox patientDetails">
	<div class="patient_actions">
		<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
	</div>
	<h4>Associated contacts:</h4>
	<div class="data_row">
		<table class="subtleWhite smallText">
			<thead>
				<tr>
					<th width="33%">Name</th>
					<th>Location</th>
					<th>Type</th>
					<?php if (BaseController::checkUserLevel(4)) {?><th colspan="2"></th><?php }?>
				</tr>
			</thead>
			<tbody id="patient_contacts">
				<?php foreach ($this->patient->contactAssignments as $pca) {
					$this->renderPartial('_patient_contact_row',array('pca'=>$pca));
				}?>
			</tbody>
		</table>
	</div>
	<?php if (BaseController::checkUserLevel(4)) {?>
	<div class="data_tow">
		<span>Add contact:</span>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>"contactname",
			'id'=>"contactname",
			'value'=>'',
			'source'=>"js:function(request, response) {

				$('#btn-add-contact').hide();

				var filter = $('#contactfilter').val();

				$('img.loader').show();

				$.ajax({
					'url': '" . Yii::app()->createUrl('patient/possiblecontacts') . "',
					'type':'GET',
					'data':{'term': request.term, 'filter': filter},
					'success':function(data) {
						data = $.parseJSON(data);

						var result = [];

						contactCache = {};

						for (var i = 0; i < data.length; i++) {
							if (data[i]['contact_location_id']) {
								if ($.inArray(data[i]['contact_location_id'], currentContacts['locations']) == -1) {
									result.push(data[i]['line']);
									contactCache[data[i]['line']] = data[i];
								}
							} else {
								if ($.inArray(data[i]['contact_id'], currentContacts['contacts']) == -1) {
									result.push(data[i]['line']);
									contactCache[data[i]['line']] = data[i];
								}
							}
						}

						response(result);

						$('img.loader').hide();

						if (filter != 'users') {
							$('#btn-add-contact').show();
						}
					}
				});
			}",
			'options'=>array(
				'minLength'=>'3',
				'select'=>"js:function(event, ui) {
					var value = ui.item.value;

					$('#contactname').val('');

					if (contactCache[value]['contact_location_id']) {
						var querystr = 'patient_id=".$this->patient->id."&contact_location_id='+contactCache[value]['contact_location_id'];
					} else {
						var querystr = 'patient_id=".$this->patient->id."&contact_id='+contactCache[value]['contact_id'];
					}

					$.ajax({
						'type': 'GET',
						'url': '".Yii::app()->createUrl('patient/associatecontact')."?'+querystr,
						'success': function(html) {
							if (html.length >0) {
								$('#patient_contacts').append(html);
								if (contactCache[value]['contact_location_id']) {
									currentContacts['locations'].push(contactCache[value]['contact_location_id']);
								} else {
									currentContacts['contacts'].push(contactCache[value]['contact_id']);
								}

								$('#btn-add-contact').hide();
							}
						}
					});

					return false;
				}",
			),
			'htmlOptions'=>array(
				'placeholder' => 'search for contacts'
			),
		));
		?>
		&nbsp;
		&nbsp;&nbsp;
		<select id="contactfilter" name="contactfilter">
			<?php foreach (ContactLabel::getList() as $key => $name) {?>
				<option value="<?php echo $key?>"><?php echo $name?>
			<?php }?>
		</select>
		&nbsp;
		<div style="display: inline-block; width: 15px;">
			<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" />
		</div>
		<?php if (BaseController::checkUserLevel(4)) {?>
			&nbsp;
			<button id="btn-add-contact" class="classy green mini" type="button" style="display: none;"><span class="button-span button-span-green">Add</span></button>
			<div id="add_contact" style="display: none;">
				<?php
				$form = $this->beginWidget('CActiveForm', array(
						'id'=>'add-contact',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'sliding'),
						'action'=>array('patient/addContact'),
				))?>

				<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
				<input type="hidden" name="contact_label_id" id="contact_label_id" value="" />

				<div>
					<div class="label">Type:</div>
					<div class="data contactType"></div>
				</div>

				<div>
					<div class="label">Institution:</div>
					<div class="data"><?php echo CHtml::dropDownList('institution_id','',CHtml::listData(Institution::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Select -'))?></div>
				</div>

				<div class="siteID">
					<div class="label">Site:</div>
					<div class="data"><?php echo CHtml::dropDownList('site_id','',array(),array('- Select -'))?></div>
				</div>

				<div class="contactLabel">
					<div class="label">Label:</div>
					<div class="data"><?php echo CHtml::dropDownList('label_id','',CHtml::listData(ContactLabel::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Select -'))?></div>
				</div>

				<div>
					<div class="label">Title:</div>
					<div class="data"><?php echo CHtml::textField('title','')?></div>
				</div>

				<div>
					<div class="label">First name:</div>
					<div class="data"><?php echo CHtml::textField('first_name','')?></div>
				</div>

				<div>
					<div class="label">Last name:</div>
					<div class="data"><?php echo CHtml::textField('last_name','')?></div>
				</div>

				<div>
					<div class="label">Nick name:</div>
					<div class="data"><?php echo CHtml::textField('nick_name','')?></div>
				</div>

				<div>
					<div class="label">Primary phone:</div>
					<div class="data"><?php echo CHtml::textField('primary_phone','')?></div>
				</div>

				<div>
					<div class="label">Qualifications:</div>
					<div class="data"><?php echo CHtml::textField('qualifications','')?></div>
				</div>

				<div class="add_contact_form_errors" style="height: auto;"></div>

				<div align="right" style="margin-top: 10px;">
					<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_contact_loader" style="display: none;" />
					<button class="classy green mini btn_save_contact" type="submit"><span class="button-span button-span-green">Save</span></button>
					<button class="classy red mini btn_cancel_contact" type="submit"><span class="button-span button-span-red">Cancel</span></button>
				</div>

				<div align="left" style="float: left; margin-top: -27px;">
					<button class="classy blue mini btn_add_site" type="submit"><span class="button-span button-span-blue">Add site/institution</span></button>
				</div>

				<?php $this->endWidget()?>
			</div>

			<div id="edit_contact" style="display: none;">
				<?php
				$form = $this->beginWidget('CActiveForm', array(
						'id'=>'edit-contact',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'sliding'),
						'action'=>array('patient/editContact'),
				))?>

				<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
				<input type="hidden" name="contact_id" id="contact_id" value="" />
				<input type="hidden" name="pca_id" id="pca_id" value="" />

				<div>
					<div class="label">Contact:</div>
					<div class="data editContactName"></div>
				</div>

				<div>
					<div class="label">Institution:</div>
					<div class="data"><?php echo CHtml::dropDownList('institution_id','',CHtml::listData(Institution::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Select -'))?></div>
				</div>

				<div class="siteID">
					<div class="label">Site:</div>
					<div class="data"><?php echo CHtml::dropDownList('site_id','',array(),array('- Select -'))?></div>
				</div>

				<div class="edit_contact_form_errors" style="height: auto;"></div>

				<div align="right" style="margin-top: 10px;">
					<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="edit_contact_loader" style="display: none;" />
					<button class="classy green mini btn_save_editcontact" type="submit"><span class="button-span button-span-green">Save</span></button>
					<button class="classy red mini btn_cancel_editcontact" type="submit"><span class="button-span button-span-red">Cancel</span></button>
				</div>

				<div align="left" style="float: left; margin-top: -27px;">
					<button class="classy blue mini btn_add_site" type="submit"><span class="button-span button-span-blue">Add site/institution</span></button>
				</div>

				<?php $this->endWidget()?>
			</div>
			<div id="add_site_dialog" title="Add site or institution" style="display: none;">
				<div>
					<p>
						This form allows you to send a request to the OpenEyes support team to add a site or institution to the system for you.
					</p>
					<?php
					$form = $this->beginWidget('CActiveForm', array(
						'id'=>'add_site_form',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'sliding'),
						'action'=>array('patient/sendSiteMessage'),
					))?>
						<div>
							<div class="label">From:</div>
							<div class="data"><?php echo CHtml::textField('newsite_from',User::model()->findByPk(Yii::app()->user->id)->email)?></div>
						</div>
						<div>
							<div class="label">Subject:</div>
							<div class="data"><?php echo CHtml::textField('newsite_subject','Please add the following site/institution')?></div>
						</div>
						<div style="height: 10em;">
							<div class="label">Message:</div>
							<div class="data"><?php echo CHtml::textArea('newsite_message',"Please could you add the following site/institution to OpenEyes:\n\n",array('rows'=>7,'cols'=>55))?></div>
						</div>
					<?php $this->endWidget()?>
					<p>
						We will respond to your request via email as soon as it has been completed.
					</p>
					<div>
						<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
							<button type="submit" class="classy green venti btn_add_site_ok"><span class="button-span button-span-green">Send</span></button>
							<button type="submit" class="classy red venti btn_add_site_cancel"><span class="button-span button-span-red">Cancel</span></button>
							<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
						</div>
					</div>
				</div>
			</div>
		<?php }?>
	</div>
	<?php }?>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#btn-add-contact').click(function() {
		if ($('#add_contact').is(':hidden')) {
			$('#add_contact').slideToggle('fast');
			$('#contact_label_id').val($('#contactfilter').val());
			if ($('#contactfilter').val() == 'nonophthalmic') {
				$('div.contactLabel').show();
			} else {
				$('div.contactLabel').hide();
			}

			$('#add_contact .data.contactType').text($('#contactfilter option:selected').text());
			$('#add_contact #site_id').html('<option value="">- Select -</option>');
			$('#add_contact .siteID').hide();
			$('#add_contact #institution_id').val('');
			$('#add_contact #title').val('');
			$('#add_contact #first_name').val('');
			$('#add_contact #last_name').val('');
			$('#add_contact #nick_name').val('');
			$('#add_contact #primary_phone').val('');
			$('#add_contact #qualifications').val('');
			$('#btn-add-contact').hide();
		}
	});

	$('#contactfilter').change(function() {
		if (!$('#add_contact').is(':hidden')) {
			$('#add_contact').slideToggle('fast');
		}
		$('#btn-add-contact').hide();
	});

	$('#add_contact #institution_id').change(function() {
		var institution_id = $(this).val();

		if (institution_id != '') {
			$.ajax({
				'type': 'GET',
				'dataType': 'json',
				'url': baseUrl+'/patient/institutionSites?institution_id='+institution_id,
				'success': function(data) {
					var options = '<option value="">- Select -</option>';
					for (var i in data) {
						options += '<option value="'+i+'">'+data[i]+'</option>';
					}
					$('#add_contact #site_id').html(options);
					sort_selectbox($('#add_contact #site_id'));
					if (i >0) {
						$('#add_contact .siteID').show();
					} else {
						$('#add_contact .siteID').hide();
					}
				}
			});
		} else {
			$('#add_contact .siteID').hide();
		}
	});

	$('button.btn_cancel_contact').click(function(e) {
		e.preventDefault();
		$('#add_contact').slideToggle('fast');
		$('#btn-add-contact').hide();
	});

	handleButton($('button.btn_save_contact'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'dataType': 'json',
			'data': $('#add-contact').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'url': baseUrl+'/patient/validateSaveContact',
			'success': function(data) {
				$('div.add_contact_form_errors').html('');
				if (data.length == 0) {
					$('img.add_contact_loader').show();
					$('#add-contact').submit();
					return true;
				} else {
					for (var i in data) {
						$('div.add_contact_form_errors').append('<div class="errorMessage">'+data[i]+'</div>');
					}
					enableButtons();
				}
			}
		});
	});

	$('a.editContact').die('click').live('click',function(e) {
		e.preventDefault();

		var location_id = $(this).parent().parent().attr('data-attr-location-id');
		var pca_id = $(this).parent().parent().attr('data-attr-pca-id');

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/getContactLocation?location_id='+location_id,
			'success': function(data) {
				editContactSiteID = data['site_id'];
				$('#edit_contact #institution_id').val(data['institution_id']);
				$('#edit_contact #institution_id').change();
				$('#edit_contact .editContactName').text(data['name']);
				$('#edit_contact #contact_id').val(data['contact_id']);
				$('#edit_contact #pca_id').val(pca_id);
			}
		});

		if ($('#edit_contact').is(':hidden')) {
			$('#edit_contact').slideToggle('fast');
		}
	});

	$('#edit_contact #institution_id').change(function() {
		var institution_id = $(this).val();

		if (institution_id != '') {
			$.ajax({
				'type': 'GET',
				'dataType': 'json',
				'url': baseUrl+'/patient/institutionSites?institution_id='+institution_id,
				'success': function(data) {
					var options = '<option value="">- Select -</option>';
					for (var i in data) {
						options += '<option value="'+i+'">'+data[i]+'</option>';
					}
					$('#edit_contact #site_id').html(options);
					sort_selectbox($('#edit_contact #site_id'));
					if (i >0) {
						$('#edit_contact .siteID').show();
					} else {
						$('#edit_contact .siteID').hide();
					}

					if (editContactSiteID) {
						$('#edit_contact #site_id').val(editContactSiteID);
						editContactSiteID = null;
					}
				}
			});
		} else {
			$('#edit_contact .siteID').hide();
		}
	});

	$('button.btn_save_editcontact').click(function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'dataType': 'json',
			'data': $('#edit-contact').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'url': baseUrl+'/patient/validateEditContact',
			'success': function(data) {
				$('div.edit_contact_form_errors').html('');
				if (data.length == 0) {
					$('img.edit_contact_loader').show();
					$('#edit-contact').submit();
					return true;
				} else {
					for (var i in data) {
						$('div.edit_contact_form_errors').append('<div class="errorMessage">'+data[i]+'</div>');
					}
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_editcontact').click(function(e) {
		e.preventDefault();

		if (!$('#edit_contact').is(':hidden')) {
			$('#edit_contact').slideToggle('fast');
		}
	});

	$('button.btn_add_site').click(function(e) {
		e.preventDefault();

		$('#newsite_from').val('<?php echo User::model()->findByPk(Yii::app()->user->id)->email?>');
		$('#newsite_subject').val('Please add the following site/institution');
		$('#newsite_message').val("Please could you add the following site/institution to OpenEyes:\n\n");

		$('#add_site_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		$('#newsite_message').focus();
		var length = $('#newsite_message').val().length;
		$('#newsite_message').selectRange(length,length);
	});

	$('button.btn_add_site_cancel').click(function(e) {
		e.preventDefault();
		$('#add_site_dialog').dialog('close');
	});

	$('button.btn_add_site_ok').click(function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'data': $('#add_site_form').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'url': baseUrl+'/patient/sendSiteMessage',
			'success': function(html) {
				if (html == "1") {
					$('#add_site_dialog').dialog('close');
					alert("Your request has been sent, we aim to process requests within 1 working day.");
				} else {
					alert("There was an unexpected error sending your message, please try again or contact support for assistance.");
				}
			}
		});
	});
});

$.fn.selectRange = function(start, end) {
	return this.each(function() {
		if (this.setSelectionRange) {
			this.focus();
			this.setSelectionRange(start, end);
		} else if (this.createTextRange) {
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', end);
			range.moveStart('character', start);
			range.select();
		}
	});
};

var editContactSiteID = null;

</script>
