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
		<h3 class="georgia">Search contacts</h3>
		<div>
			<form id="admin_contacts">
				<div>
					<span class="label">Search:</span>
					<?php echo CHtml::textField('q',@$_GET['q'])?>
				</div>
				<div>
					<span class="label">Label:</span>
					<?php echo CHtml::dropDownList('label',@$_GET['label'],CHtml::listData(ContactLabel::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Any label -'))?>
				</div>
				<div>
					<span class="label"></span>
					<?php echo EventAction::button('Search', 'search', array('colour' => 'blue'))->toHtml()?>
					<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" />
				</div>
			</div>
		</form>
	</div>
</div>
<?php if (@$contacts) {?>
	<div id="searchResults" class="curvybox white">
		<?php echo $this->renderPartial('/admin/_contacts_list',array('contacts'=>$contacts))?>
	</div>
<?php }?>
<script type="text/javascript">
	var resultCache = {};

	$(document).ready(function() {
		$('#q').select().focus();

		handleButton($('#et_search'),function(e) {
			e.preventDefault();
			if ($('#q').length <1) {
				alert("Please enter a search term");
				enableButtons();
				$('#q').focus();
			} else {
				window.location.href = baseUrl+'/admin/contacts?q='+$('#q').val()+'&label='+$('#label').val();
			}
		});

		$('li.even, li.odd').die('click').live('click',function(e) {
			e.preventDefault();
			window.location.href = baseUrl+'/admin/editContact?contact_id='+$(this).attr('data-attr-id');
		});
	});
</script>
