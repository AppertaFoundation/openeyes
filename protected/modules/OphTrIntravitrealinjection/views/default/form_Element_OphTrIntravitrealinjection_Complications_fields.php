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

<?php
$html_options = array(
    'options' => array(),
    'empty' => 'Select',
    'div_id' => get_class($element) . '_' . $side . '_complications',
    'label' => 'Complications',
);
$complications = OphTrIntravitrealinjection_Complication::model()->activeOrPk($element->complicationValues)->findAll();
foreach ($complications as $complication) {
    $html_options['options'][(string)$complication->id] = array('data-order' => $complication->display_order, 'data-description_required' => $complication->description_required);
}
echo $form->multiSelectList(
    $element,
    get_class($element) . '[' . $side . '_complications]',
    $side . '_complications',
    'id',
    CHtml::listData($complications, 'id', 'name'),
    $element->ophtrintravitinjection_complication_defaults,
    $html_options,
    false,
    false,
    null,
    false,
    false,
    array('field' => 6)
)
?>
<?php
$show_desc = false;
if (@$_POST[get_class($element)] && $complication_ids = @$_POST[get_class($element)][$side . '_complications']) {
    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $complication_ids);
    $complications = OphTrIntravitrealinjection_Complication::model()->findAll($criteria);
} else {
    $complications = $element->{$side . '_complications'};
}

if ( is_array($complications) ) {
    foreach ($complications as $complication) {
        if ($complication->description_required) {
            $show_desc = true;
        }
    }
}
?>

<div id="div_Element_OphTrIntravitrealinjection_Complications_<?php echo $side; ?>_oth_descrip" class="data-group"
     style="display: <?php if (!$show_desc) {
            echo ' none';
                     } ?>">
  <div class="cols-<?php echo $form->columns('label'); ?>">
    <label for="<?php echo get_class($element) ?>_<?php echo $side . '_oth_descrip' ?>">
        <?php echo $element->getAttributeLabel($side . '_oth_descrip'); ?>:
    </label>
  </div>
  <div class="cols-<?php echo $form->columns('field'); ?>">
        <?php echo $form->textArea($element, $side . '_oth_descrip', array('rows' => 4, 'cols' => 30, 'nowrapper' => true)); ?>
  </div>
</div>

