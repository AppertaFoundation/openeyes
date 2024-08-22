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
$la_hidden = false;
$sed_hidden = false;
if (count($element->anaesthetic_type) > count($element->anaesthetic_type_assignments)) {
    if (count($element->anaesthetic_type) == 0) {
        $la_hidden = true;
        $sed_hidden = true;
    } elseif (
        count($element->anaesthetic_type) == 1
        && ($element->anaesthetic_type[0]->code == 'GA' || $element->anaesthetic_type[0]->code == 'NoA')
    ) {
        $la_hidden = true;
        $sed_hidden = true;
    } elseif (count($element->anaesthetic_type) == 1 && $element->anaesthetic_type[0]->code === 'Sed') {
        $la_hidden = true;
    }
} else {
    if (count($element->anaesthetic_type_assignments) == 0) {
        $la_hidden = true;
        $sed_hidden = true;
    } elseif (
        count($element->anaesthetic_type_assignments) == 1
        && (
            $element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'GA'
            || $element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'NoA'
        )
    ) {
        $la_hidden = true;
        $sed_hidden = true;
    } elseif (count($element->anaesthetic_type_assignments) == 1 && $element->anaesthetic_type_assignments[0]->anaesthetic_type->code === 'Sed') {
        $la_hidden = true;
    }
}

$is_outpatient_minor_op = isset($data['outpatient_minor_op']) && $data['outpatient_minor_op'];
$anaesthetic_ids = array_map(
    static function ($item) {
        return $item->id;
    },
    $element->anaesthetic_type
);
$delivery_ids = array_map(
    static function ($item) {
        return $item->id;
    },
    $element->anaesthetic_delivery
);

$type_options = array();
$delivery_options = array();

if ($prefilled) {
    foreach ($template_data['anaesthetic_type'] as $anaesthetic_type_id) {
        $type_options[$anaesthetic_type_id] = array('data-prefilled-value' => 'true');
    }
    foreach ($template_data['anaesthetic_delivery'] as $anaesthetic_type_id) {
        $delivery_options[$anaesthetic_type_id] = array('data-prefilled-value' => 'true');
    }
}

?>

<div class="element-fields full-width flex-layout flex-top" id="OphTrOperationnote_Anaesthetic"
     data-outpatient-minor-op="<?= $is_outpatient_minor_op ? 'yes' : 'no' ?>">
    <div class="cols-11 flex-layout flex-top col-gap">
        <div class="cols-8">
            <table class="last-left">
                <colgroup>
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <tr>
                    <td>Type</td>
                    <td>
                        <?= $form->checkBoxes(
                                $element,
                                'AnaestheticType',
                                'anaesthetic_type',
                                null,
                                false,
                                false,
                                false,
                                false,
                                array(
                                    'label-class' => $element->getError('anaesthetic_type') ? 'error' : '',
                                    'options' => $type_options,
                                    'extra_fieldset_attributes' => [
                                        'data-test' => 'anaesthetic-type',
                                    ]
                                ),
                                array('field' => 12)) ?>
                    </td>
                </tr>
                <tr id="Element_OphTrOperationnote_Anaesthetic_AnaestheticDelivery_container"
                    style="<?= $la_hidden ? 'display: none;' : '' ?>">
                    <td>LA Delivery Methods</td>
                    <td>
                        <?php echo $form->checkBoxes(
                            $element,
                            'AnaestheticDelivery',
                            'anaesthetic_delivery',
                            null,
                            false,
                            false,
                            false,
                            false,
                            array(
                                'label-class' => $element->getError('anaesthetic_delivery') ? 'error' : '',
                                'options' => $delivery_options,
                            )
                        ); ?>
                    </td>
                </tr>
                <tr id="Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_container"
                    style="<?= $sed_hidden ? 'display: none;' : ''?>">
                    <td>
                        Given by:
                    </td>
                    <td>
                        <fieldset id="<?= \CHtml::modelName($element) . '_anaesthetist_id' ?>">
                            <?php echo $form->radioButtons(
                                $element,
                                'anaesthetist_id',
                                'Anaesthetist',
                                $element->anaesthetist_id,
                                false,
                                false,
                                false,
                                false,
                                array(
                                    'nowrapper' => true,
                                    'label-class' => $element->getError('Anaesthetist') ? 'error' : '',
                                    'prefilled_value' => $template_data['anaesthetist_id'] ?? ''
                                )
                            ); ?>
                        </fieldset>
                    </td>
                </tr>
                <?php if ($element->getSetting('fife')) : ?>
                    <tr>
                        <td>
                            <?php echo $element->getAttributeLabel('anaesthetic_witness_id') ?>
                        </td>
                        <td>
                            <?php echo $form->dropDownList(
                                $element,
                                'anaesthetic_witness_id',
                                CHtml::listData($element->surgeons, 'id', 'FullName'),
                                array(
                                    'empty' => 'Select',
                                    'nowrapper' => true,
                                    'data-prefilled-value' => $template_data['anaesthetic_witness_id'] ?? '',
                                ),
                                $element->witness_hidden,
                                array('field' => 3)
                            ); ?>

                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="cols-4">
            <table>
                <colgroup>
                    <col class="cols-4"/>
                    <col class="cols-8"/>
                </colgroup>
                <tbody>
                <tr>
                    <td>Agents</td>
                    <td class="cols-8">
                        <?php echo $form->multiSelectList(
                            $element,
                            'AnaestheticAgent',
                            'anaesthetic_agents',
                            'id',
                            $this->getAnaesthetic_agent_list($element),
                            null,
                            array(
                                'empty' => '- Anaesthetic agents -',
                                'label' => 'Agents',
                                'hidedropdown' => true,
                                'nowrapper' => true,
                            ),
                            false,
                            false,
                            null,
                            false,
                            false,
                            array('field' => 3)
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td>Complications</td>
                    <td>
                        <?php echo $form->multiSelectList(
                            $element,
                            'OphTrOperationnote_AnaestheticComplications',
                            'anaesthetic_complications',
                            'id',
                            CHtml::listData(
                                OphTrOperationnote_AnaestheticComplications::model()->activeOrPk($element->anaestheticComplicationValues)->findAll(),
                                'id',
                                'name'
                            ),
                            array(),
                            array(
                                'empty' => '- Complications -',
                                'label' => 'Complications',
                                'class' => 'hidden',
                                'nowrapper' => true,
                            ),
                            false,
                            false,
                            null,
                            false,
                            false,
                            array('field' => 12)
                        ) ?>
                    </td>
                </tr>
                <tr id="Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_container"
                    style="<?=!$element->anaesthetic_comment ? 'display: none;' : ''?>"
                    class="comment-group js-comment-container"
                    data-comment-button="#Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_button">
                    <td>
                        Comments
                    </td>
                    <td>
                        <?php echo $form->textArea(
                            $element,
                            'anaesthetic_comment',
                            array('nowrapper' => true),
                            false,
                            array(
                                'rows' => 4,
                                'cols' => 40,
                                'class' => 'js-comment-field autosize',
                                'data-prefilled-value' => $template_data['anaesthetic_comment'] ?? ''
                            )
                        ) ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="add-data-actions flex-item-bottom">
        <div class="flex-item-bottom">
            <button id="Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_button"
                    class="button js-add-comments"
                    type="button"
                    data-comment-container="#Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_container"
                    style="<?= $element->anaesthetic_comment ? 'visibility: hidden;' : '' ?>">
                <i class="oe-i comments small-icon"></i>
            </button>
            <button class="button hint green js-add-select-search" id="add-anaesthetic-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button><!-- popup to add data to element -->
        </div>
    </div>
</div>
<?php
$complications = OphTrOperationnote_AnaestheticComplications::model()->activeOrPk($element->anaestheticComplicationValues)->findAll();
$agents = $this->getAnaesthetic_agent_list($element);
?>

<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-anaesthetic-btn'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($key, $value) {
                        return ['label' => $value, 'id' => $key];
                    },
                        array_keys($agents),
                        $agents)
                ) ?>, {'header': 'Agents', 'id': 'AnaestheticAgent', 'multiSelect': true}),
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($item) {
                        return [
                            'label' => $item->name,
                            'id' => $item->id,
                        ];
                    },
                        $complications)
                ) ?>, {
                    'header': 'Complications',
                    'id': 'OphTrOperationnote_AnaestheticComplications',
                    'multiSelect': true
                })
            ],
            onReturn: function (adderDialog, selectedItems) {
                for (i in selectedItems) {
                    var id = selectedItems[i]['id'];
                    var $selector = $('#' + selectedItems[i]['itemSet'].options['id']);
                    $selector.val(id);
                    $selector.trigger('change');
                }
                return true;
            }
        });
    });
</script>
