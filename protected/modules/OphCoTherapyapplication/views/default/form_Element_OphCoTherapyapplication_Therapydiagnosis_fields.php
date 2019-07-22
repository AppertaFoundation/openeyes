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
// have to work around the bad processing of the elements at this point, and check for a POSTed disorder that is not
// in the standard list.
if (!empty($_POST)) {
    $posted_l2_id = @$_POST[get_class($element)][$side . '_diagnosis2_id'];

    if ($posted_l1_id = @$_POST[get_class($element)][$side . '_diagnosis1_id']) {
        $l1_seen = false;
        foreach ($l1_disorders as $l1) {
            if ($l1->id == $posted_l1_id) {
                $l1_seen = true;
                // append l2 if necessary
                if ($posted_l2_id) {
                    $l2_seen = false;
                    foreach ($l1_opts[$l1->id]['data-level2'] as $l2_disorder_struct) {
                        if ($l2_disorder_struct['id'] == $posted_l2_id) {
                            $l2_seen = true;
                            break;
                        }
                    }
                    if (!$l2_seen) {
                        if ($l2_disorder = Disorder::model()->findByPk($posted_l2_id)) {
                            $l2_disorders[$l1->id][] = $l2_disorder;
                            $l1_opts[$l1->id]['data-level2'][] = array(
                                'id' => $posted_l2_id,
                                'term' => $l2_disorder->term,
                            );
                        }
                    }
                }
                break;
            }
        }
        if (!$l1_seen) {
            $l1_disorders[] = Disorder::model()->findByPk($posted_l1_id);
        }
    }
}
// now manipulation is at an end, we can json encode the level 2 disorder data
foreach ($l1_opts as $id => $data) {
    if (array_key_exists('data-level2', $data)) {
        $l1_opts[$id]['data-level2'] = CJSON::encode($data['data-level2']);
    }
}

$layoutColumns = array('label' => 4, 'field' => 8);
?>
<div class="data-group">
    <div class="cols-<?php echo $layoutColumns['label']?> column">
        <label for="<?php echo get_class($element).'_'.$side.'_diagnosis1_id';?>">
            <?php echo $element->getAttributeLabel($side.'_diagnosis1_id'); ?>:
        </label>
    </div>
    <div class="cols-<?php echo $layoutColumns['field']?> column end">
        <?php $form->widget('application.widgets.DiagnosisSelection', array(
                'field' => $side.'_diagnosis1_id',
                'element' => $element,
                'options' => CHtml::listData($l1_disorders, 'id', 'term'),
                'layout' => 'search',
                'default' => false,
                'nowrapper' => true,
                'dropdownOptions' => array(
                    'empty' => 'Select',
                    'options' => $l1_opts,
                ),
        ));?>
    </div>
</div>
<div class="row<?php if (!array_key_exists($element->{$side.'_diagnosis1_id'}, $l2_disorders)) {
    echo ' hidden';
               }?>" id="<?php echo $side ?>_diagnosis2_wrapper">
    <div class="cols-<?php echo $layoutColumns['label']?> column">
        <label for="<?php echo get_class($element).'_'.$side.'_diagnosis2_id';?>">
            <?php echo $element->getAttributeLabel($side.'_diagnosis2_id'); ?>:
        </label>
    </div>
    <div class="cols-<?php echo $layoutColumns['field']?> column">
        <?php
        $l2_attrs = array('empty' => 'Select');
        $l2_opts = array();
        if (array_key_exists($element->{$side.'_diagnosis1_id'}, $l2_disorders)) {
            $l2_opts = $l2_disorders[$element->{$side.'_diagnosis1_id'}];
            // this is used in the javascript for checking the second level list is correct.
            $l2_attrs['data-parent_id'] = $element->{$side.'_diagnosis1_id'};
        }
        $form->widget('application.widgets.DiagnosisSelection', array(
            'field' => $side.'_diagnosis2_id',
            'element' => $element,
            'options' => CHtml::listData($l2_opts, 'id', 'term'),
            'layout' => 'search',
            'label' => false,
            'default' => false,
            'nowrapper' => true,
            'dropdownOptions' => $l2_attrs,
        ));?>
    </div>
</div>
