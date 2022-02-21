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
<section class="element <?php echo $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>"
        <?php if ($this->action->Id === 'view') : ?>
            data-element-id="<?php echo $element->id; ?>"
        <?php endif; ?>
>
    <?php if ($this->action->Id === 'view') {
            $form=$this->beginWidget(
                'CActiveForm',
                array(
                    "action"=>"/OphInDnaextraction/default/updateDnaTests/".$element->event_id,
                    'htmlOptions' => ['class' => 'frmDnaTests']
                )
            );
    } ?>


    <?=\CHtml::activeHiddenField($element, "id"); ?>

  <input type="hidden" name="<?=\CHtml::modelName($element); ?>[force_validation]"/>
  <fieldset class="dnatests element-fields">
    <div class="data-group">
      <div class="cols-3 column">
        <label>Tests:</label>
      </div>
      <div class="cols-9 data-group column">
        <table>
          <thead>
          <tr>
            <th>Date</th>
            <th>Study</th>
            <th>Volume</th>
            <th>Withdrawn by</th>
            <th></th>
          </tr>
          </thead>
          <tbody class="transactions">

            <?php
            $is_test = true;
                /* as OphInDnaextraction_DnaTests_Transaction is  not an element, OE will not take care of the POSTed values and Models for is */
                $transaction_posts = Yii::app()->request->getPost('OphInDnaextraction_DnaTests_Transaction');

            if ($transaction_posts) {
                foreach ($transaction_posts as $i => $transaction_post) {
                    $transaction = $this->getTransactionModel($transaction_post, $element->id);

                    $disabled = !$this->checkAccess('TaskEditGeneticsWithdrawals');
                    $this->renderPartial('application.modules.OphInDnaextraction.views.default._dna_test', array('transaction' => $transaction, 'i' => $i, 'disabled' => ($this->action->id === 'view') ));
                }
            } elseif ($element->transactions) {
                foreach ($element->transactions as $i => $transaction) {
                    $disabled = !$this->checkAccess('TaskEditGeneticsWithdrawals');
                    $this->renderPartial('application.modules.OphInDnaextraction.views.default._dna_test', array('transaction' => $transaction, 'i' => $i, 'disabled' => ($this->action->id === 'view') ));
                }
            } else {
                $is_test = false;
            } ?>

            <tr>
              <td class="no-tests <?php echo $is_test ? 'hidden' : ''; ?> " colspan="4">
                  No tests have been logged for this DNA.
              </td>
          </tr>
          </tbody>
        </table>
            <?php if ($this->action->Id === 'view') : ?>
              <div class="button-bar right">
                  <span style="display: none"><i class="frmDnaTests_loader loadervfa fa-spinner fa-spin"></i></span>
                  <span class="frmDnaTests_successmessage successmessage msg success" style="display: none; font-size: 12px;"><i class="oe-i tick small"></i> Saved</span>
                  <div class="frmDnaTests_controls" class="frmDnaTests_controls" style="display: none;">
                      <button class="button warning small cancelTest">Cancel</button>
                      <button class="button small default submitTest">Save changes</button>
                  </div>
              </div>
            <?php endif; ?>
        <button class="button small secondary addTest">
          Add
        </button>
      </div>
    </div>
  </fieldset>
    <?php if ($this->action->Id === 'view') {
        $this->endWidget();
    } ?>
</section>
