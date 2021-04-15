<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.GenericFormJSONConverter.js'), CClientScript::POS_HEAD);
foreach (Yii::app()->user->getFlashes() as $key => $message) {
    echo '<div class="flash- alert-box with-icon warning' . $key . '">' . $message . "</div>\n";
}
?>

<form method="POST" action="/OphDrPrescription/OphDrPrescriptionAdmin/localDrugsAdmin/EditLocalDrugInstitutionMappings">
    <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <?php
    $columns = array(
        array(
            'header' => 'Preferred Term',
            'name' => 'preferred_term',
            'type' => 'raw',
            'htmlOptions'=>array('width'=>'300px'),
            'value' => function ($data, $row) {
                return $data->preferred_term;
            }
        ),
        array(
            'header' => 'Assigned to current institution',
            'type' => 'raw',
            'name' => 'selected',
            'value' => function($data, $row) {
                return CHtml::checkBox("selected[$row]", $data->hasMapping(ReferenceData::LEVEL_INSTITUTION, $data->getIdForLevel(ReferenceData::LEVEL_INSTITUTION)));
            }
        ),
        array(
            'header' => '',
            'name' => 'id',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return CHtml::hiddenField("id[$row]", $data->id);
            }
        ),
    );

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'generic-admin standard',
        'columns' => $columns
    ));
    ?>
    <div>
        <button class="generic-admin-save button large" name="admin-save" type="submit"id="et_admin-save">Save</button>&nbsp;
    </div>
</form>