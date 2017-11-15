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
<div class="element-fields" id="OphTrOperationnote_Anaesthetic">

    <?php

        $is_hidden = function() use ($element){
          if( count($element->anaesthetic_type_assignments) == 1 && ( $element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'GA' || $element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'NoA' ) ){
              return true;
          }
            return false;
        };

        echo $form->checkBoxes($element, 'AnaestheticType', 'anaesthetic_type', 'Type');

        echo $form->checkBoxes($element, 'AnaestheticDelivery', 'anaesthetic_delivery', 'LA Delivery Methods',
        false, false, false, false, array('fieldset-class' => ($is_hidden() ? 'hidden' : '')));

        echo $form->radioButtons($element, 'anaesthetist_id', 'Anaesthetist', $element->anaesthetist_id, false, false, false, false,
            array('fieldset-class' => ($is_hidden() ? 'hidden' : '')) );
    ?>

    <?php if ($element->getSetting('fife')) { ?>
        <?php echo $form->dropDownList($element, 'anaesthetic_witness_id', CHtml::listData($element->surgeons, 'id', 'FullName'), array('empty' => '- Please select -'),
            $element->witness_hidden, array('field' => 3)); ?>
    <?php } ?>


    <div class="row">
        <div class="large-6 column">
            <div id="<?php echo 'div_' . CHtml::modelName($element); ?>" class="row field-row widget">

                <div class="large-2 column">
                    <label for="AnaestheticAgent">Agents:</label>
                </div>

                <div class="large-7 large-offset-2 column end">
                    <?php echo $form->multiSelectList(
                        $element,
                        'AnaestheticAgent',
                        'anaesthetic_agents',
                        'id',
                        $this->getAnaesthetic_agent_list($element),
                        null,
                        array('empty' => '- Anaesthetic agents -', 'label' => 'Agents', 'nowrapper' => true),
                        false,
                        false,
                        null,
                        false,
                        false,
                        array('field' => 3)
                    ) ?>
                </div>
            </div>

            <div id="Element_OphTrOperationnote_Anaesthetic_anaesthetic_complications" class="row field-row widget">

                <div class="large-2 column">
                    <label for="OphTrOperationnote_AnaestheticComplications">Complications:</label>
                </div>

                <div class="large-7 large-offset-2 column end">
                    <?php echo $form->multiSelectList(
                      $element,
                      'OphTrOperationnote_AnaestheticComplications',
                      'anaesthetic_complications',
                      'id',
                      CHtml::listData(OphTrOperationnote_AnaestheticComplications::model()->activeOrPk($element->anaestheticComplicationValues)->findAll(), 'id', 'name'),
                      array(),
                      array('empty' => '- Complications -', 'label' => 'Complications', 'nowrapper' => true),
                      false,
                      false,
                      null,
                      false,
                      false,
                      array('field' => 3)
                  ) ?>
                </div>

            </div>

        </div>
        <div class="large-6 column">

            <div class="large-8 column end">
                <label for="Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment">Comments:</label>
                <?php echo $form->textArea($element, 'anaesthetic_comment', array('nowrapper' => true), false, array('rows' => 4)) ?>

            </div>


        </div>
</div>