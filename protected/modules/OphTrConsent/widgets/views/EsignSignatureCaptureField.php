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
/**  @var \OEModule\OphTrConsent\widgets\EsignSignatureCaptureField $this */
$el_class = get_class($element);
$widget_class = get_class($this);
$uid = \CHtml::modelName($el_class) . "_" . \CHtml::modelName($widget_class) . "_" . $row_id;
$name_prefix =  \CHtml::modelName($this->element)."[signatures][{$this->row_id}]";
?>
<tr id="<?= $uid ?>" data-row_id="<?= $row_id ?>">
    <?php $this->renderHiddenFields(); ?>
    <!-- Row num -->
    <?php if(strcmp($row_id, "X") == 0) { ?>
        <td><span class="highlighter red">X</span></td>
    <?php } else { ?>
        <td><span class="highlighter js-row-num"></span></td>
    <?php } ?>
    <!-- Role -->
    <td>
        <span class="js-signatory-label">
            <?= CHtml::encode($this->signature->signatory_role) ?>
        </span>
    </td>
    <!-- Name -->
    <td>
        <span class="js-signatory-name">
            <?= \CHtml::encode($this->signature->signatory_name) ?>
        </span>
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
            <button type="button" class="js-device-sign-btn">e-Sign using linked device</button>
        </div>
    </td>
</tr>
<script type="text/javascript">
    $(function(){
        new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
            signature_type: <?= $this->signature->type ?>,
            element_id: <?= $this->element->id ?? "null" ?>
        });
    });
</script>