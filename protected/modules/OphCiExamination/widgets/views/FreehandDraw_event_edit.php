<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$annotate_tools_icon_url = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-annotate-tools.svg';
$model_name = CHtml::modelName($element);
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('FreehandDraw.js') ?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/fabric/dist/fabric.min.js') ?>"></script>

<div class="element-fields full-width">
        <?php
        $last_key = array_key_last($element->entries);
        foreach ($element->entries as $i => $entry) {
            $this->render(
                'FreehandDrawEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . ($i) . ']',
                    'row_count' => $i,
                    'annotate_tools_icon_url' => $annotate_tools_icon_url
                )
            );

            echo $last_key === $i  ? "" : "<hr class='divider js-divider-{$i}'>";
        }
        ?>

    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search" id="show-add-template-popup">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
<script>
    const freehandDrawController = new OpenEyes.OphCiExamination.FreehandDraw();

    const template_list = <?= CJSON::encode(
        array_map(function ($item) {
            return [
                'label' => $item->name,
                'id' => $item->id,
                'date-template-url' => $item->protected_file->getDownloadURL(),
                'filename' => explode('.', $item->protected_file->name)[0],
                'full_name' => '',
                'created_date' => '',
            ];
        }, DrawingTemplate::model()->active()->findAll())
    ) ?>;
    new OpenEyes.UI.AdderDialog({
        openButton: $('#show-add-template-popup'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(template_list)],
        onReturn: function (adderDialog, selectedItems) {
            freehandDrawController.addTemplate(selectedItems[0]);
        }
    });

    document.querySelectorAll('.oe-annotate-image').forEach(template => {
        const image_url = template.dataset.templateUrl;
        const row_key = template.dataset.key;
        freehandDrawController.initTemplate(image_url, row_key, false);
    });


</script>

<script type="text/template" id="new_drawing_template" style="display:none">
    <?php
        $this->render(
            'FreehandDrawEntry_event_edit',
            array(
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'annotate_tools_icon_url' => $annotate_tools_icon_url,
                'row_count' => '{{row_count}}',
                'values' => array(
                    'id' => '',
                    'protected_file_id' => '',
                    'template_url' => '{{template_url}}',
                    'filename' => '{{filename}}',
                    'full_name' => '{{full_name}}',
                    'is_edited' => 1,
                    'date' => '{{date}}',
                    'comments' => ''
                ),
            )
        );
        ?>
</script>
