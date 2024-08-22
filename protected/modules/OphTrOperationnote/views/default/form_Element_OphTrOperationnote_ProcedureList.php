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

if ($element->booking_event_id) {
    $whiteboard_display_mode = SettingMetadata::model()->getSetting('opnote_whiteboard_display_mode');

    if ($whiteboard_display_mode === 'CURRENT') {
        array_unshift(
            $this->event_actions,
            EventAction::link(
                'Display Whiteboard',
                '#',
                null,
                array('class' => 'small button', 'id' => 'js-display-whiteboard', 'data-id' => $element->booking_event_id)
            ),
            EventAction::link(
                'Close Whiteboard',
                '#',
                null,
                array('class' => 'small button', 'id' => 'js-close-whiteboard', 'data-id' => $element->booking_event_id)
            )
        );
    } else {
        array_unshift(
            $this->event_actions,
            EventAction::link(
                'Display Whiteboard',
                $this->createUrl('default/whiteboard/' . $element->booking_event_id),
                null,
                array('class' => 'small button', 'target' => '_blank')
            )
        );
    }
}
?>
<section class="element edit full edit-procedures  <?php echo $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">

  <header class="element-header">
    <h3 class="element-title">Procedures</h3>
  </header>

  <div class="element-fields full-width">
    <table class="cols-full last-left">
      <colgroup>
        <col class="cols-4">
        <col class="cols-8">
      </colgroup>
      <tbody>
      <tr>
        <td style="vertical-align: top;">
          
            <?php echo $form->hiddenInput($element, 'booking_event_id') ?>
            <?php echo $form->radioButtons(
                $element,
                'eye_id',
                $element->eyeOptions,
                ($element->eye() ? $element->eye()->id : null),
                null,
                null,
                null,
                null,
                array(
                  'nowrapper' => true,
                  'test' => 'procedure-side',
                  'prefilled_value' => $template_data['eye_id'] ?? '',
                ),
                array()
            ) ?>
        </td>

        <td class="">
            <?php
            $form->widget('application.widgets.ProcedureSelection', array(
                'element' => $element,
                'selected_procedures' => $element->procedures,
                'newRecord' => true,
                'last' => true,
                'label' => '',
                'hidden' => ($this->action->id == 'create'
                    && $element->eye == null
                    && !@$_POST['Element_OphTrOperationnote_ProcedureList']['eye_id']),
                'templates' => $data['templates_for_unbooked'] ?? []
            ));
            ?>
          <style>
            #typeProcedure {align-items: flex-start;}
            #procedure-selector-container {padding-right: 28px;}
            #procedure-selector-container fieldset{min-width: 100%}
            #select_procedure_id_procs {min-width: 100%; max-width: 100%;}
          </style>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

</section>

<div class="sub-elements active">
</div>
