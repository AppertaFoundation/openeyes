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
?>

<?php Yii::app()->clientScript->registerPackage('rrule'); ?>

<?php if (@$htmlOptions['nowrapper']) {?>
    <?=\CHtml::textField($name, $value, $htmlOptions)?>
<?php } else {?>
    <div id="div_<?=\CHtml::modelName($element)?>_<?php echo $field?>" class="data-group"<?php if (@$htmlOptions['hide']) {
        ?> style="display: none;"<?php
                 }?>>
        <div class="cols-<?php echo $layoutColumns['label'];?> column">
            <?php
            $labelText = empty($htmlOptions['label']) ? CHtml::encode($element->getAttributeLabel($field)) : $htmlOptions['label'];
            $labelText .= ':';
            echo Chtml::label($labelText, Chtml::getIdByName($name));
            ?>
        </div>
        <div class="cols-<?php echo $layoutColumns['field'];?> column<?php if (empty($htmlOptions['append-text']) || empty($layoutColumns['append-text'])) {
            ?> end<?php
                         }?>">
            <?=\CHtml::textField($name, $value, $htmlOptions)?>
            <?php if (!empty($links)) {
                foreach ($links as $link) {
                    echo '<span class="field-info">'.CHtml::link($link['title'], $link['href'], array('id' => $link['id'])).'</span>';
                }
            }?>
        </div>
        <?php if (!empty($htmlOptions['append-text']) && !empty($layoutColumns['append-text'])) {?>
            <div class="large-<?php echo $layoutColumns['append-text'];?> column collapse in end">
                <span class="field-info"><?php echo $htmlOptions['append-text'];?></span>
            </div>
        <?php }?>
    </div>
<?php }?>

<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.Widgets.RRuleField(
            $('#<?= @$htmlOptions['id'] ?: CHtml::getIdByName($name) ?>')
        );
    });
</script>

