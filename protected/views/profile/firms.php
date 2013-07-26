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
		<h3 class="georgia">Firms you work in</h3>
		<div>
			<form id="profile_firms" method="post" action="/profile/firms">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" /></span>
						<span class="column_name">Name</span>
						<span class="column_subspecialty">Subspecialty</span>
					</li>
					<div class="sortable">
						<?php
						foreach ($user->firmSelections as $i => $firm) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $firm->id?>">
								<span class="column_checkbox"><input type="checkbox" name="firms[]" value="<?php echo $firm->id?>" /></span>
								<span class="column_name"><?php echo $firm->name?></span>
								<span class="column_subspecialty"><?php echo $firm->subspecialtyText?></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
	<div>
		Add firm: <?php echo CHtml::dropDownList('profile_firm_id','',$user->getNotSelectedFirmList(),array('empty'=>'- Select -'))?>
		<?php echo CHtml::link('Add all','#',array('id'=>'add_all'))?>
	</div>
</div>
<div style="margin-bottom:1em;">
	Note: you can also set the sites you work at, <?php echo CHtml::link('click here',Yii::app()->createUrl('/profile/sites'))?> to do so.
</div>
<div>
	<?php echo EventAction::button('Delete', 'delete', array('colour' => 'blue'))->toHtml()?>
</div>
<script type="text/javascript">
	$('#profile_firm_id').change(function(e) {
		var firm_id = $(this).val();

		if (firm_id != '') {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/profile/addfirm',
				'data': 'firm_id='+firm_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					}
				}
			});
		}

		$(this).val('');
	});

	$('#checkall').click(function() {
		$('input[name="firms[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

	$('#et_delete').click(function() {
		$('#profile_firms').submit();
	});

	$('#add_all').click(function() {
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/profile/addfirm',
			'data': 'firm_id=all&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'success': function(html) {
				if (html == "1") {
					window.location.reload();
				} else {
					alert("Something went wrong trying to add the firms.	Please try again or contact support for assistance.");
				}
			}
		});
	});
</script>
