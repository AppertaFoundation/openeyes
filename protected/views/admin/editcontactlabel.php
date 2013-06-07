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
		<h3 class="georgia">Edit contact label</h3>
		<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
		<div>
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'editContactLabelForm',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus'=>'#ContactLabel_name'
			))?>
			<?php echo $form->textField($contactlabel,'name')?>
			<?php $this->endWidget()?>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Save', 'save', array('colour' => 'green'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('colour' => 'red'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete', array('colour' => 'blue'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/contactlabels';
	});

	handleButton($('#et_save'),function(e) {
		e.preventDefault();
		$('#editContactLabelForm').submit();
	});

	handleButton($('#et_delete'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/deleteContactLabel',
			'data': 'contact_label_id=<?php echo $contactlabel->id?>',
			'success': function(response) {
				if (response == 0) {
					window.location.href = baseUrl+'/admin/contactLabels';
				} else {
					alert("You cannot delete this contact label because it's in use by "+response+" contacts.");
					enableButtons();
				}
			}
		});
	});

	function sort_selectbox(element) {
		rootItem = element.children('option:first').text();
		element.append(element.children('option').sort(selectSort));
	}

	function selectSort(a, b) {
		if (a.innerHTML == rootItem) {
			return -1;
		}
		else if (b.innerHTML == rootItem) {
			return 1;
		}
		return (a.innerHTML > b.innerHTML) ? 1 : -1;
	};

	var rootItem = null;
</script>
