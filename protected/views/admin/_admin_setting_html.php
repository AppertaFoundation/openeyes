<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $currentValue = $metadata->getSettingName(null, $allowed_classes, $institution_id, true);
;
if (!isset($currentValue) || $currentValue == null) {
    // we need this to prevent HTML value without =
    $currentValue = '';
}

Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('/js/oeadmin/OpenEyes.HTMLSettingEditorController.js'), \CClientScript::POS_HEAD);
?>

<?php
    $substitutions = array_merge(
        SettingMetadata::getSessionSubstitutions(),
        SettingMetadata::getPatientSubstitutions(),
        SettingMetadata::getCorrespondenceSubstitutions()
    );
    ?>

<h2>Letterhead</h2>
<h3>Set up the letterhead for correspondence</h3>
<div class="admin-correspondence-letterhead edit">
    <?= \CHtml::textArea($metadata->key, $currentValue, ['class' => 'cols-full']); ?>
    <hr class="divider"/>
    <div class="flex-layout flex-top">
        <div class="cols-4">
            <h3> Quick text insert</h3>
            <p>Position the cursor in the editor then click the buttons to insert the required content. Placeholder text will be inserted where available.</p>
            <h3>Single line return</h3>
            <p>Use SHIFT + ENTER to create a single line return. ENTER will create a new Paragraph.</p>
            <h3>Reinstating default header</h3>
            <p>Leaving this setting blank will cause OpenEyes to fall back to the default letter header.</p>
        </div>
        <div class="cols-7">
            <div class="editor-quick-insert-btns">
                <?php if (isset($substitutions)) { ?>
                    <?php foreach ($substitutions as $key => $value) { ?>
                        <button type="button" class="quick-insert" data-insert="<?= $key ?>"><?= $value['label'] ?></button>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?= \CHtml::hiddenField('hidden_'.$metadata->key, 1); ?>
<script type="text/javascript">
    $(document).ready(function () {
        let html_editor_controller =
            new OpenEyes.HTMLSettingEditorController(
                "<?= $metadata->key ?>",
                <?= json_encode(\Yii::app()->params['tinymce_default_options'])?>,
                <?= json_encode($substitutions) ?>
            );
    });
</script>