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
/**  @var EsignPINField $this */
$el_class = get_class($element);
$widget_class = get_class($this);
$uid = $el_class . "_" . $widget_class . "_" . $row_id;
?>
<tr id="<?= $uid ?>" data-row_id="<?= $row_id ?>">
    <!-- Row num -->
    <td><span class="highlighter js-row-num"></span></td>
    <!-- Role -->
    <td><span class="js-signatory-label"><?= $this->signatory_label ?></span></td>
    <!-- Name -->
    <td>
        <span class="js-signatory-name">
            <?php echo CHtml::hiddenField(
                'signatory_id_'.$uid,
                '',
                array('class' => "user-user_id-entry js-user_id-input")
            ) ?>
            <?php $this->widget('application.widgets.AutoCompleteSearch',array(
                'field_name' => 'signatory_name_'.$uid,
                'htmlOptions' => array('placeholder' => 'Type a signatory name to search')
            )); ?>
        </span>
    </td>
    <!-- Date -->
    <td>
        <div class="js-signature-date" <?php if(!$this->isSigned()) { echo 'style="display:none"'; }?>>
            <?php $this->displaySignatureDate() ?>
        </div>
        <div class="js-signature-control" <?php if($this->isSigned()) { echo 'style="display:none"'; }?>>
            <div class="oe-user-pin">
                <?php echo CHtml::passwordField('pin_'.$uid, '', array(
                    'placeholder' => "********",
                    'maxlength' => 8,
                    'inputmode' => "numeric",
                    'class' => "user-pin-entry js-pin-input"
                )); ?>
                <button type="button" class="try-pin js-sign-button">PIN sign</button>
            </div>
        </div>
    </td>
    <!-- Signature -->
    <td>
        <div class="js-signature-wrapper flex-l" <?php if(!$this->isSigned()) { echo 'style="display:none"'; }?>>
            <?php $this->displaySignature() ?>
            <div class="esigned-at">
                <i class="oe-i tick-green small pad-right"></i>Signed <small>at</small> <span class="js-signature-time">??:??</span>
            </div>
        </div>
    </td>
</tr>
<script type="text/javascript">
    $(function(){
        new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
            submitAction: "<?=$this->getAction()?>",
            needUserName: true
        });

        if(OpenEyes.UI.AutoCompleteSearch !== undefined){
            OpenEyes.UI.AutoCompleteSearch.init({
                input: $('#signatory_name_<?= $uid ?>'),
                url: '/user/autoComplete',
                onSelect: function(){
                    let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                    $('#signatory_id_<?= $uid ?>').val(AutoCompleteResponse.id);
                    $('#signatory_name_<?= $uid ?>').val(AutoCompleteResponse.label);
                }
            });
        }
    });
</script>