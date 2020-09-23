<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var Element_OphTrOperationchecklists_ProcedureList $element
 * @var bool $isCollapsable
 */
?>

<?php if (isset($isCollapsable) && $isCollapsable) { ?>
    <header class="subgroup-header">
        <h3><?= $element->getElementTypeName(); ?></h3>
        <div class="viewstate-icon">
            <i class="oe-i small js-element-subgroup-viewstate-btn collapse" data-subgroup="subgroup-procedure-list"></i>
        </div>
    </header>
<?php } ?>
<div class="element-data full-width"
     id="subgroup-procedure-list" <?= (isset($isCollapsable) && $isCollapsable) ? 'style= "display: none"' : '' ?>>
    <div class="cols-12">
        <table class="cols-full last-left large-text">
            <tbody>
            <tr>
                <td>
                    Procedure
                </td>
                <td>
                    <table>
                        <tbody>
                        <?php foreach ($element->procedures as $procedure) : ?>
                            <tr>
                                <td>
                                    <span class="priority-text">
                                        <?php echo $element->eye->adjective ?>

                                        <?php echo $procedure->term ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="1">
                    Disorder
                </td>
                <td colspan="5">
                    <?= \Disorder::model()->findByPk($element->disorder_id)->term; ?>
                </td>
            </tr>
            <tr>
                <td colspan="1">
                    Priority
                </td>
                <td colspan="5">
                    <?= \OphTrOperationbooking_Operation_Priority::model()->findByPk($element->priority_id)->name; ?>
                </td>
            </tr>
            <tr>
                <td colspan="1">
                    Anaesthetic
                </td>
                <td colspan="5">
                    <?= $element->getAnaestheticTypeDisplay() ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    let elementClass = '<?= get_class($element) ?>';
    let anaestheticTypeQuestionRelationIds;

    $(document).ready(function () {
        let isGAorSedAnaestheticType = '<?= json_encode($element->isGAorSedAnaestheticType()); ?>';
        anaestheticTypeQuestionRelationIds = <?= json_encode(OphTrOperationchecklists_Questions::getAnaestheticTypeQuestionRelationIds()); ?>;
        if (isGAorSedAnaestheticType === 'true') {
            showAnaestheticTypeQuestions();
        } else {
            hideAnaestheticTypeQuestions();
        }
    });

    function hideAnaestheticTypeQuestions() {
        anaestheticTypeQuestionRelationIds.forEach(function(id) {
            $('tr[id=' + id + ']').hide();
        });
    }

    function showAnaestheticTypeQuestions() {
        anaestheticTypeQuestionRelationIds.forEach(function(id) {
            $('tr[id=' + id + ']').show();
        });
    }
</script>
