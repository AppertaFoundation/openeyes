<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php if (!@$htmlOptions['nowrapper']) { ?>
<div class="row field-row"<?php if (@$htmlOptions['hidden']) { ?> style="display: none;"<?php } ?>>
    <?php unset($htmlOptions['hidden']) ?>

    <div class="large-<?php echo $layoutColumns['label']; ?> column">
        <label for="<?php echo CHtml::modelName($element) . '_' . $field . '_0'; ?>">
            <?php echo CHtml::encode($element->getAttributeLabel($field)) ?>:
        </label>
    </div>
    <div class="large-<?php echo $layoutColumns['field']; ?> column end">
<?php } ?>

    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => $name,
        'id' => CHtml::modelName($element) . '_' . $field . '_0',
        // additional javascript options for the date picker plugin
        'options' => array_merge($options, array(
            'showAnim' => 'fold',
            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
        )),
        'value' => (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value) ? Helper::convertMySQL2NHS($value) : $value),
        'htmlOptions' => $htmlOptions,
    )); ?>

<?php if (!@$htmlOptions['nowrapper']) { ?>
    </div>
</div>
<?php } ?>
