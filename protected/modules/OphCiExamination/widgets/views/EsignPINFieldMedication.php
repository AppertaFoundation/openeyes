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
?>
<?php
/** @var \OEModule\OphCiExamination\widgets\EsignPINFieldMedication $this */
/** @var string $row_id */
$el_class = CHtml::modelName($this->element);

$uid = $el_class . "_" . $row_id;
$read_only = $this->element->event && date('Y-m-d', strtotime($this->element->event->event_date)) !== date('Y-m-d');
foreach ($element->getInfoMessages() as $msg) { ?>
   <div class="alert-box info"><?=CHtml::encode($msg)?></div>
<?php }
if (!$element->isSigned()) {
    if (strlen($element->getUnsignedMessage()) > 0) {
        ?>
        <div class="alert-box issue">
            <?= $element->getUnsignedMessage() ?>
        </div>
        <?php
    }
} ?>

<div id="<?= $uid ?>" data-row_id="<?= $row_id ?>">

    <?php $this->renderHiddenFields(); ?>

    <div class="js-signature-control flex-r" <?php if ($this->isSigned()) { echo 'style="display:none"'; } ?>>
        <label class="highlight inline">
            <?php echo \CHtml::checkBox(
                $el_class . "[save_draft_prescription]",
                $element->save_draft_prescription,
                [
                    'value' => ($element->save_draft_prescription === false) ? 0 : 1
                ]
            )?>
            Save as draft prescription
        </label>
        <span class="tabspace"></span>
        <div class="oe-user-pin" <?php if ($element->save_draft_prescription === true) { echo 'style="display:none"'; } ?>>
            <?php echo CHtml::passwordField('pin_'.$uid, '', array(
                'placeholder' => "********",
                'maxlength' => 8,
                'inputmode' => "numeric",
                'class' => "user-pin-entry js-pin-input"
            )); ?>
            <button type="button" class="try-pin js-sign-button">Sign by PIN</button>
        </div>
    </div>

    <div class="mm-signature-row flex-r" <?php if (!$this->isSigned()) { echo 'style="display:none"'; }?>>
        <div class="large-text">
            <small class="fade"><?= $this->signature->signatory_role ?></small> <?= $this->signature->signatory_name ?>
            <span class="tabspace"></span>
        </div>
        <div class="flex-l">
            <div class="js-signature-wrapper"><?= $this->displaySignature() ?></div>
            <div class="esigned-at">
                <i class="oe-i tick-green small pad-right"></i>
                Signed <small>at</small> <span class="js-signature-time"><?= $this->displaySignatureTime() ?></span>
            </div>
        </div>
    </div>

</div>

<hr class="divider">

<script type="text/javascript">
    $(function(){
        new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
            submitAction: "<?=$this->getAction()?>",
            signature_type: <?= $this->signature->type ?>,
            element_id: <?= $this->element->id ?? "null" ?>
        });

        $('#<?= $el_class ?>_save_draft_prescription').on('click', function(){
            let save_draft = $(this).val() === '1';
            $(this).val(save_draft ? '0' : '1');

            if($(this).val() === '0'){
                $('.oe-user-pin').show();
            } else {
                $('.oe-user-pin').hide();
                $('#<?= $el_class ?>_signatures_0_id').val('');
                $('#<?= $el_class ?>_signatures_0_proof').val('');
            }
        });

        <?php if (!$read_only) { ?>
            const setVisibleSignatureRow = () => {
                const objects = $('.js-btn-prescribe');
                const signature_row = $('#MedicationManegement_Signature_row');
                signature_row.hide();
                let checked = false;

                if(objects.length > 0){
                    for(let i = 0; i<objects.length; i++){
                        const $input = $(objects[i]).closest(".toggle-switch").find("input");
                        if($input.prop("checked")){
                            checked = true;
                            break;
                        }
                    }

                    if(checked === true){
                        signature_row.show();
                    }
                }
            }

            setVisibleSignatureRow();

            $('.<?=$el_class?>').on("click", ".js-btn-prescribe", function () {
                setTimeout(function() {
                    setVisibleSignatureRow();
                }, 50);
            });

            const setToDefaultSignatureRow = () => {
                $('.js-signature-control').show();
                $('.mm-signature-row').hide();
                $('.js-signature-wrapper').html('');
                $('#<?= $el_class ?>_signatures_0_id').val('');
                $('#<?= $el_class ?>_signatures_0_proof').val('');
            }

            let isset_mm_error = document.getElementById("OEModule_OphCiExamination_models_MedicationManagement_element").getElementsByClassName("error")[0];
            if(typeof isset_mm_error === "undefined"){
                sessionStorage.setItem('mmesign_change', 'false');
            }

            if (sessionStorage.getItem("mmesign_change") === null) {
                sessionStorage.setItem('mmesign_change', 'false');
            }

            if (sessionStorage.getItem("mmesign_change") === "true") {
                setToDefaultSignatureRow();
            }

            class MedManagementTableChanges {
                constructor() {
                    let MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                    this.observer = new MutationObserver( mutations  => {
                        mutations.forEach( mutation => {
                            const newNodes = mutation.addedNodes;
                            newNodes.forEach(node => {
                                sessionStorage.setItem('mmesign_change', 'true');
                                setToDefaultSignatureRow();
                            });

                            const removedNodes = mutation.removedNodes;
                            removedNodes.forEach(node => {
                                sessionStorage.setItem('mmesign_change', 'true');
                                setToDefaultSignatureRow();
                            });
                        });
                    });
                }

                subscribeObserver( element ){
                    this.observer.observe(element, {
                        attributes: true,
                        characterData: true,
                        childList: true,
                        subtree: true,
                        attributeOldValue: true,
                        characterDataOldValue: true
                    });
                }

                subscribeInputEvent( selector ){
                    let rxElement = document.querySelectorAll( selector );
                    for(let i = 0; i < rxElement.length; i++) {
                        rxElement[i].addEventListener("input", function() {
                            sessionStorage.setItem('mmesign_change', 'true');
                            setToDefaultSignatureRow();
                        });
                    }
                }
            }

            const rxTable = document.getElementById("OEModule_OphCiExamination_models_MedicationManagement_entry_table");
            if(rxTable !== null ){
                const mm_signature = new MedManagementTableChanges();
                mm_signature.subscribeObserver(rxTable);
                mm_signature.subscribeInputEvent( ".js-dose" );
                mm_signature.subscribeInputEvent( ".js-frequency" );
                mm_signature.subscribeInputEvent( ".js-route" );
                mm_signature.subscribeInputEvent( ".js-duration" );
                mm_signature.subscribeInputEvent( ".js-dispense-location" );
            }

            const signature_wrapper = document.querySelector('.js-signature-wrapper');
            const signature_wrapper_observer = new MutationObserver(function(mutations) {

                if(signature_wrapper.innerHTML.length !== 0 ){
                    $('.mm-signature-row').show();
                }
            });
            signature_wrapper_observer.observe(signature_wrapper, { attributes: true, childList: true, characterData: true });
        <?php } else { ?>
            $('.js-signature-control :input').attr('disabled', true);
        <?php }?>
    });
</script>