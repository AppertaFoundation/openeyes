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
<div class="admin box">
	<h2>Add location</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<div class="row field-row">
		<div class="large-2 column">
			<div class="field-label">Contact:</div>
		</div>
		<div class="large-10 column">
			<div class="field-value"><?php echo $contact->fullName?></div>
		</div>
	</div>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
		'enableAjaxValidation'=>false,
		'focus'=>'#username'
	))?>
		<input type="hidden" name="contact_id" value="<?php echo $contact->id?>" />
		<div class="row field-row">
			<div class="large-2 column">
				<label for="institution_id">Institution:</label>
			</div>
			<div class="large-5 column end">
				<?php echo CHtml::dropDownList('institution_id',@$_POST['institution_id'],CHtml::listData(Institution::model()->active()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Please select -'))?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<label for="site_od">Site:</label>
			</div>
			<div class="large-5 column end">
				<?php echo CHtml::dropDownList('site_id','',$sites,array('empty' => '- Optional -'))?>
			</div>
		</div>
		<?php echo $form->formActions(); ?>
		<?php $this->endWidget()?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#User_username').focus();

		$('#institution_id').change(function() {
			var institution_id = $(this).val();

			if (institution_id != '') {
				$.ajax({
					'type': 'GET',
					'dataType': 'json',
					'url': baseUrl+'/admin/getInstitutionSites?institution_id='+institution_id,
					'success': function(sites) {
						var options = '<option value="">- Optional -</option>';
						for (var i in sites) {
							options += '<option value="'+i+'">'+sites[i]+'</option>';
						}
						$('#site_id').html(options);
						sort_selectbox($('#site_id'));
					}
				});
			}
		});
	});


	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editContact?contact_id=<?php echo $contact->id?>';
	});

	handleButton($('#et_save'),function(e) {
		e.preventDefault();
		$('#adminform').submit();
	});

	function sort_selectbox(element)
	{
		rootItem = element.children('option:first').text();
		element.append(element.children('option').sort(selectSort));
	}

	function selectSort(a, b)
	{
		if (a.innerHTML == rootItem) {
			return -1;
		} else if (b.innerHTML == rootItem) {
			return 1;
		}
		return (a.innerHTML > b.innerHTML) ? 1 : -1;
	};

	var rootItem = null;
</script>
