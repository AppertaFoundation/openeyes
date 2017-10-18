<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php $address_targets = $address_targets + array('OTHER' => 'Other'); ?>

<?php
    $is_editable_contact_targets = isset($is_editable_contact_targets) ? $is_editable_contact_targets : true;
    $is_editable_contact_name = isset($is_editable_contact_name) ? $is_editable_contact_name : true;
    $is_editable_address = isset($is_editable_address) ? $is_editable_address : true;


?>


<?php echo CHtml::dropDownList('',
        null,
        $address_targets,
        array(
            'empty' => '- Recipient -',
            'nowrapper' => true, 
            'class' => 'full-width docman_recipient',
            'data-rowindex' => $row_index, 
            'data-previous' => $contact_id,
            'data-name' => 'DocumentTarget['.$row_index.'][attributes][contact_id]',
            'id' => 'docman_recipient_' . $row_index,
            'disabled' => !$is_editable_contact_targets,
            'style' => (!$is_editable_contact_targets ? 'background-color: lightgray' : ''),
        )
    );

    echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_id]', $contact_id);
?>
<br><br>
<?php echo CHtml::textField('DocumentTarget['.$row_index.'][attributes][contact_name]', $contact_name, array('readonly' => !$is_editable_contact_name)); ?>
<div>
    <textarea rows="4" cols="10" <?php echo !$is_editable_address ? 'readonly' : ''; ?> name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $address; ?></textarea>
</div>