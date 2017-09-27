<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
	<h2>Edit common drugs list</h2>

	<?php
    $modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'));
    Yii::app()->clientScript->registerScriptFile($modulePath.'/js/commondrugsadmin.js', CClientScript::POS_HEAD);
    echo CHtml::beginForm('CommonDrugs', 'get', array('id' => 'set_site_subspec_form'));
    ?>

	<div class="row field-row">
		<div class="large-1 column"><label for="site_id">Site:</label></div>
		<div class="large-4 column">
			<?php
            echo CHtml::dropDownList('site_id', $selectedsite,
                CHtml::listData(Site::model()->findAll(), 'id', 'short_name'));
            ?>
		</div>
		<div class="large-2 column"><label for="site_id">Subspeciality:</label></div>
		<div class="large-4 column end">
			<?php
            echo CHtml::dropDownList('subspecialty_id', $selectedsubspecialty,
                CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'), array('empty' => '-- Select --'));
            ?>
		</div>

	</div>

	<?php
    echo CHtml::endForm();
    ?>
	<table class="generic-admin" id="common_drugs_list">
		<thead>
		<tr>
			<th>Drug name</th>
			<th>Dose unit</th>
			<th>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php
        foreach ($site_subspecialty_drugs as $siteSubspecialtyDrug) {
            echo '<tr>
							<td>'.$siteSubspecialtyDrug->drugs->name.'</td>
							<td>'.$siteSubspecialtyDrug->drugs->dose_unit.'</td>
							<td>
								<a OnCLick="DeleteCommonDrug('.$siteSubspecialtyDrug->id.')">Delete</a></td>
						</tr>';
        }
        ?>
		<tr>
			<td colspan="2">
				<?php
                $defaultURL = '/'.Yii::app()->getModule('OphDrPrescription')->id.'/'.Yii::app()->getModule('OphDrPrescription')->defaultController;

                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'drug_id',
                    'id' => 'autocomplete_drug_id',
                    'source' => "js:function(request, response) {
									$.getJSON('".$defaultURL."/DrugList', {
										term : request.term,
										type_id: $('#drug_type_id').val(),
										preservative_free: ($('#preservative_free').is(':checked') ? '1' : ''),
									}, response);
								}",
                    'options' => array(
                        'select' => "js:function(event, ui) {
										addItem(ui.item.id);
										$(this).val('');
										return false;
									}",
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'search for drugs',
                    ),
                )); ?>

			</td>
			<td>
				<b>&lt;-- Select from list to add</b>
			</td>
		</tr>
		</tbody>
	</table>

</div>


