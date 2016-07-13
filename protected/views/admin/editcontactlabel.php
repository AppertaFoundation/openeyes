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
	<h2>Edit contact label</h2>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'editContactLabelForm',
		'enableAjaxValidation'=>false,
		'focus'=>'#ContactLabel_name',
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5
		)
	))?>
		<?php echo $form->textField($contactlabel,'name',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		<?php echo $form->formActions(array(
			'cancel-uri' => '/admin/contactlabels',
			'delete' => 'Delete'
		));?>
	<?php $this->endWidget()?>
</div>
<script type="text/javascript">
	handleButton($('#et_delete'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/deleteContactLabel',
			'data': 'contact_label_id=<?php echo $contactlabel->id?>&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'success': function(response) {
				if (response == 0) {
					window.location.href = baseUrl+'/admin/contactLabels';
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "You cannot delete this contact label because it's in use by "+response+" contacts."
					}).open();
					enableButtons();
				}
			}
		});
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
