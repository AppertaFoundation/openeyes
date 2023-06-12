<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

$url = Yii::app()->assetManager->getPublishedPathOfAlias('application.modules.OphCiExamination.assets');
\Yii::app()->clientScript->registerScriptFile($url . '/js/IntraocularPressure.js');

?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryIOP.js') ?>"></script>

<?php
$model_name = CHtml::modelName($element);
$pastIOPs = $this->getPastIOPs();
?>

<?php if ($this->element) :
    echo \CHtml::activeHiddenField($this->element, "id");
endif; ?>

<div class="element-fields element-eyes" data-test="iop-history-element">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) :?>
        <div class="cols-6 js-element-eye <?=$eye_side?>-eye <?=$page_side?>" data-side="<?=$eye_side?>">
            <div class="active-form data-group">
                <?php $this->render(
                    'HistoryIOP_event_edit_side',
                    [
                        'side' => $eye_side,
                        'element' => $this->element,
                        'form' => $form,
                        'model_name' => $model_name,
                        'pastIOPs' => $pastIOPs,
                    ]
                )?>
            </div>
            <div class="inactive-form" style="display: none;">
                <div class="add-side">
                    <a href="#">
                        Add <?=$eye_side?> eye
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
