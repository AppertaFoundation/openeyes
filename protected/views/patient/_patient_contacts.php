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
					<?php if (BaseController::checkUserLevel(3)) {?><th colspan="2"></th><?php }?>
				</tr>
			</thead>
			<tbody id="patient_contacts">
				<?php foreach ($this->patient->contactAssignments as $pca) {
					$this->renderPartial('_patient_contact_row',array('pca'=>$pca));
				}?>
			</tbody>
		</table>
	</div>
	<?php if (BaseController::checkUserLevel(3)) {?>
	<div class="data_tow">
		<span>Add contact:</span>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>"contactname",
			'id'=>"contactname",
			'value'=>'',
			'source'=>"js:function(request, response) {

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
							var index = $.inArray(data[i]['contact_location_id'], currentContacts);
							if (index == -1) {
								result.push(data[i]['line']);
								contactCache[data[i]['line']] = data[i];
							}
						}

						response(result);

						$('img.loader').hide();
					}
				});
			}",
			'options'=>array(
				'minLength'=>'3',
				'select'=>"js:function(event, ui) {
					var value = ui.item.value;

					$('#contactname').val('');

					var querystr = 'patient_id=".$this->patient->id."&contact_location_id='+contactCache[value]['contact_location_id'];

					$.ajax({
						'type': 'GET',
						'url': '".Yii::app()->createUrl('patient/associatecontact')."?'+querystr,
						'success': function(html) {
							if (html.length >0) {
								$('#patient_contacts').append(html);
								currentContacts.push(contactCache[value]['contact_location_id']);
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
			<option value="">- Filter -</option>
			<option value="users" selected="selected"><?php echo ContactLabel::staffType()?></option>
			<option value="Consultant Ophthalmologist">Consultant ophthalmologist</option>
			<option value="nonophthalmic">Non-ophthalmic specialist</option>
		</select>
		&nbsp;
		<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" />
	</div>
	<?php }?>
</div>
