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
	<h2>Search contacts</h2>
	<form id="admin_contacts_search">
		<div class="row field-row">
			<div class="large-2 column">
				<label for="q">Search:</label>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::textField('q',@$_GET['q'])?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<label for="label">Label:</label>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::dropDownList('label',@$_GET['label'],CHtml::listData(ContactLabel::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Any label -'))?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 large-offset-2 column end">
				<?php echo EventAction::button('Search', 'search', array(), array('class' => 'small'))->toHtml()?>
				<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" />
			</div>
		</div>
	</form>
</div>
<?php if (@$contacts) {?>
		<?php echo $this->renderPartial('/admin/_contacts_list',array('contacts'=>$contacts))?>
<?php }?>
<script type="text/javascript">
	var resultCache = {};

	$(document).ready(function() {
		$('#q').select().focus();

		handleButton($('#et_search'),function(e) {
			e.preventDefault();
			if ($('#q').val().length <1) {
				new OpenEyes.UI.Dialog.Alert({
					content: "Please enter a search term"
				})
				.on('close', $('#q').focus)
				.open();
				enableButtons();
			} else {
				window.location.href = baseUrl+'/admin/contacts?q='+$('#q').val()+'&label='+$('#label').val();
			}
		});
	});
</script>
