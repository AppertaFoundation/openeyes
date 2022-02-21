<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$form = $this->beginWidget('CActiveForm', array(
    'id' => 'withdrawal-form',
    'action' => Yii::app()->createUrl('/OphTrConsent/default/withdraw?event_id='.$element->event_id),
));

?>

<div class="element-fields full-width">
    <?php if (isset($this->action->id) && $this->action->id === 'create') : ?>
        <div class="alert-box warning">Withdrawal of this event is available in view mode only.</div>
    <?php else : ?>
        <div class="alert-box warning js-withdraw-form-warning row" style="display: none; margin-top: 10px"></div>
        <div class="row flex">
            <div class="flex-l cols-6">
                <label>Reason for withdrawal:</label>
                <?php if(!isset($entry->withdrawal_reason)) {
                    echo CHtml::textArea(
                        CHtml::modelName($element).'_withdrawal_reason',
                        $entry->withdrawal_reason,
                        array(
                            'class' => 'cols-full',
                            'rows' => '1',
                            'placeholder' => "Withdrawal reason (required)"
                        )
                    );
                } else {
                    echo '<span class="highlighter">' . nl2br(CHtml::encode($entry->withdrawal_reason ?: "-")) . '</span>';
                }?>
            </div>
            
            <div>
                <?php if($entry->signature_id === NULL) { ?>
                    <button class="blue hint js-remove-withdraw">Cancel consent withdrawal</button>
                <?php } elseif (!isset($entry->withdrawal_reason)) { ?>
                    <button type="button" id="withdraw-submit-button" class="red hint">Withdraw consent</button>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?> 
    
    <hr class="divider" />
</div>

<?php  $this->endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(this).on('click','.js-remove-withdraw',function(e) {
            e.preventDefault();
            let element = document.getElementsByClassName('element ' + <?= CJavaScript::encode(CHtml::modelName($entry)) ?>)[0];
            element.dispatchEvent(new Event('element_removed'));
            removeElement(element);
            window.location.href = "<?= Yii::app()->createUrl('/OphTrConsent/default/removeWithdraw?event_id='.$element->event_id) ?>";
        });

        $(this).on('click','#withdraw-submit-button',function(e) {
            var $error = $(".js-withdraw-form-warning");
            $error.hide();

            var $reason = $("#Element_OphTrConsent_Esign_withdrawal_reason");
            if($reason.is(":visible") && $reason.val() === "") {
                $error.text("Withdrawal reason must not be blank").show();
                e.preventDefault();
            } else {
                $('form#withdrawal-form').submit();
            }
        });
	});
</script>


