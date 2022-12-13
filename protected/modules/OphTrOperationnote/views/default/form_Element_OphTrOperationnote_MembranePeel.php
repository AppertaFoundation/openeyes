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
<div class="element-fields full-width flex-layout" id="div_Element_OphTrOperationnote_GenericProcedure_comments">

  <div class="cols-11 flex-layout flex-top col-gap">
    <div class="cols-4">
      <table class="last-left">
        <colgroup>
          <col class="cols-2"/>
        </colgroup>
        <tbody>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('membrane_blue'); ?>
          </td>
          <td>
                <?php echo $form->radioBoolean($element, 'membrane_blue', array('nowrapper' => true, 'prefilled_value' => $template_data['membrane_blue'] ?? '')) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('brilliant_blue'); ?>
          </td>
          <td>
                <?php echo $form->radioBoolean($element, 'brilliant_blue', array('nowrapper' => true, 'prefilled_value' => $template_data['brilliant_blue'] ?? '')) ?>
          </td>
        </tr>
        </tbody>
      </table>

    </div>
    <div class="cols-7">

      <table>
        <colgroup>
          <col class="cols-2"/>
        </colgroup>
        <tbody>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('other_dye'); ?>
          </td>
          <td>
                <?php echo $form->textField(
                    $element,
                    'other_dye',
                    array('nowrapper' => true, 'class' => 'cols-12', 'placeholder' => 'Other dye;', 'data-prefilled-value' => $template_data['other_dye'] ?? ''),
                    array()
                ) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('comments'); ?>
          </td>
          <td>
                <?php echo $form->textArea(
                    $element,
                    'comments',
                    array('nowrapper' => true, 'class' => 'cols-11'),
                    false,
                    array('placeholder' => 'Comments', 'data-prefilled-value' => $template_data['comments'] ?? '')
                ) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
