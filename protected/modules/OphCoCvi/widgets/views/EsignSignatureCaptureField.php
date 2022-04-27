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
/**  @var EsignSignatureCaptureField $this */
$el_class = get_class($element);
$widget_class = get_class($this);
$uid = \CHtml::modelName($el_class) . "_" . \CHtml::modelName($widget_class) . "_" . $row_id;
$name_prefix =  \CHtml::modelName($this->element)."[signatures][{$this->row_id}]";
$edit_signature_role = Yii::app()->request->getParam('edit_signature_role');
?>
<?php if ($edit_signature_role) : ?>
<script type="text/javascript">
    scrollToElement($('.OEModule_OphCoCvi_models_Element_OphCoCvi_Esign'));
</script>
<?php endif; ?>

<tr id="<?= $uid ?>" data-row_id="<?= $row_id ?>">
    <?php $this->renderHiddenFields(); ?>
    <!-- Row num -->
    <td><span class="highlighter js-row-num"></span></td>
    <!-- Role -->
    <td><span class="js-signatory-label">
            <?php
            if(($edit_signature_role == 1 || (!$this->isSigned())) && !empty($roles = $this->signature->getRoleOptions())): ?>
                <?= CHtml::dropDownList(
                        $name_prefix."[signatory_role]",
                        $this->signature->signatory_role,
                        array_combine($roles, $roles),
                        [
                            "readonly" => $this->isSigned(),
                            "class" => "js-signatory_role-field",
                        ]
                ) ?>
            <?php else: ?>
                <?= CHtml::encode($this->signature->signatory_role) ?></span></td>
            <?php endif; ?>
    <!-- Name -->
    <td>
        <span class="js-signatory-name">
            <?= $this->isSigned() ? $this->signature->signatory_name : \CHtml::textField(
                    $name_prefix."[signatory_name]",
                    $this->signature->signatory_name,
                    [
                        "readonly" => $this->isSigned(),
                        "placeholder" => "Please enter name",
                        "class" => "js-signatory_name-field hidden",
                    ]
            ) ?>
        </span>
        <?= !$this->isSigned() ? '<span class="js-signatory-default-name">'. $this->signature->signatory_name .'</span>' : ''; ?>
    </td>
    <!-- Date -->
    <td>
        <div class="js-signature-date" <?php if(!$this->isSigned()) { echo 'style="display:none"'; }?>>
            <?php $this->displaySignatureDate() ?>
        </div>
    </td>
    <!-- Signature -->
    <td>
        <div class="js-signature-wrapper flex-l" <?php if(!$this->isSigned()) { echo 'style="display:none"'; }?>>
            <?php $this->displaySignature() ?>
            <div class="esigned-at">
                <i class="oe-i tick-green small pad-right"></i>Signed <small>at</small> <span class="js-signature-time">
                    <?php $this->displaySignatureTime() ?></span>
            </div>
        </div>
        <div class="js-signature-control" <?php if($this->isSigned()) { echo 'style="display:none"'; }?>>
            <button type="button" class="js-popup-sign-btn">e-Sign</button>
            <?php  if($this->controller->module->id === 'OphCoCvi'): ?>
                <button type="button" class="js-device-sign-btn">e-Sign on tablet</button>
                <button type="button" class="js-print-postal-form-btn">Print postal form</button>
            <?php else: ?>
                <button type="button" class="js-device-sign-btn">e-Sign using linked device</button>
            <?php endif; ?>
        </div>
    </td>
    <td>
        <?php
        $authRules = new AuthRules();
        if ($this->isSigned() && $authRules->canEditEvent($this->element->event)) { ?>
            <?php if (!$edit_signature_role) { ?>
                <button class="js-signature-edit">Edit</button>
                <button class="js-signature-delete">Delete</button>
            <?php } else { ?>
                <button class="js-signature-save">Save</button>
            <?php } ?>
        <?php } ?>
    </td>
</tr>
<script type="text/javascript">
    $(function(){
        new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
            signature_type: <?= $this->signature->type ?>,
            element_id: <?= $this->element->id ?? "null" ?>,
            mode: "<?= $this->mode ?>"
        });
    });

    $('.js-signatory_role-field').on('change', function(e) {
        let _sel = $(this);
        let _tr = _sel.closest('tr');
        let signatory_name_object = _tr.find('.js-signatory_name-field');
        let default_signatory_name_object = _tr.find('.js-signatory-default-name');

        if(signatory_name_object.length > 0) {
            if (_sel.val().toLowerCase() === 'patient') {
                signatory_name_object.val($.trim(default_signatory_name_object.html()));
                signatory_name_object.hide();
                default_signatory_name_object.show();
            } else {
                signatory_name_object.val('');
                signatory_name_object.show();
                default_signatory_name_object.hide();
            }
        }
    });

    $('.js-signature-save').on('click', function(e) {
        var signature_id = $(this).closest("tr").find(".js-id-field").val();
        var role_name = $(this).closest("tr").find(".js-signatory_role-field").val();

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphCoCvi/default/updateSignatureRole',
            'data': {
                signature_id: signature_id,
                role_name: role_name,
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            'success': function(resp) {
                console.log(resp);
                window.location = baseUrl + '/OphCoCvi/default/view/'+OE_event_id;
            }
        });
    });

    $('.js-signature-edit').on('click', function(e) {
        window.location = window.location.href+"?edit_signature_role=1";
    });

    $('.js-signature-delete').on('click', function(e) {
        var signature_id = $(this).closest("tr").find(".js-id-field").val();
        window.location = "/OphCoCvi/default/deleteSignature?event_id="+OE_event_id+"&signature_id="+signature_id;
    });
</script>