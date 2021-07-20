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
$class_name = get_class($element);
$row_id = $class_name.'_'.$this->row_id;
?>

<tr id="row_<?= $row_id ?>" data-row_id="<?= $row_id ?>">
    <td><span class="highlighter">1</span></td>
    <td><?= $this->signatory_label ?></td>
    <td><?php echo $this->logged_user_name ?></td>

    <?php if($this->isSigned()){ ?>
        <td>
            <?php echo Helper::convertDate2NHS($element->created_date) ?>
        </td>
        <td>
            <img src="empty">
        </td>
    <?php } else { ?>
        <td>
            <div class="oe-user-pin">
                <?php echo CHtml::passwordField('input_'.$row_id, '', array(
                    'placeholder'=>"********",
                    'maxlength'=>"8",
                    'inputmode'=>"numeric",
                    'class'=>"user-pin-entry"
                )); ?>
                <button
                    id="<?= 'button_'.$row_id ?>"
                    class="try-pin js-idg-ps-popup-btn"
                    data-action="next"
                    onclick="EsignPinWidget.getSignature('<?= $this->action ?>','<?= $row_id ?>');"
                >PIN sign</button>
            </div>
        </td>
    <?php } ?>

    <td>
        <div id="<?= 'div_'.$row_id ?>"></div>
    </td>
</tr>