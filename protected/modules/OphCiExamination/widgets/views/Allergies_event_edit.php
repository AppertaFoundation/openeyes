<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<?php
Yii::app()->clientScript->registerScriptFile($this->getJsPublishedPath('Allergies.js'), CClientScript::POS_HEAD);
$model_name = CHtml::modelName($element);
$this->render(
    'Allergies_form',
    array(
        'element' => $element,
        'model_name' => $model_name,
    )
);
?>
<input type="hidden" name="<?= $model_name ?>[present]" value="1" />

<table id="<?= $model_name ?>_entry_table">
    <thead>
    <tr>
        <th>Allergy</th>
        <th>Comments</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($element->entries as $entry) {
        $this->render(
            'AllergyEntry_event_edit',
            array(
                'entry' => $entry,
                'form' => $form,
                'model_name' => $model_name,
                'editable' => true
            )
        );
    }
    ?>
    </tbody>
</table>
</div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\AllergyEntry();
    $this->render(
        'AllergyEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'editable' => true,
            'values' => array(
                'id' => '',
                'allergy_id' => '{{allergy_id}}',
                'allergy_display' => '{{allergy_display}}',
                'other' => '{{other}}',
                'comments' => '{{comments}}',
            )
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.AllergiesController();
    });
</script>
