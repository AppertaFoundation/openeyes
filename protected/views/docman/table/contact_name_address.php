<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php if($is_editable): ?>
    <?php echo CHtml::dropDownList( ( $contact_id && is_numeric($contact_id) ? 'DocumentTarget['.$row_index.'][attributes][contact_id]' : ' '),
            $contact_id,
            $address_targets,
            array(
                'empty' => '- Recipient -',
                'nowrapper' => true, 
                'class' => 'full-width docman_recipient ' . ( $contact_id && is_numeric($contact_id) ? '' : 'hidden') ,
                'data-rowindex' => $row_index, 
                'data-previous' => $contact_id,
                'data-name' => 'DocumentTarget['.$row_index.'][attributes][contact_id]'
            )
        );
    ?>
    <?php if( !$contact_id || !is_numeric($contact_id)): ?>
        <?php echo CHtml::textField('DocumentTarget['.$row_index.'][attributes][contact_name]', $contact_name, array(
            'class' => 'docman_recipient_freetext'
        )); ?>
    <?php endif; ?>
<?php else: ?>
    <?php echo $contact_name; ?>
    <?php echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][' . (!$contact_id || !is_numeric($contact_id) ? 'contact_name' : 'contact_id') . ']', (!$contact_id || !is_numeric($contact_id) ? $contact_name : $contact_id)); ?>
<?php endif; ?>
<div>
    <?php if($is_editable): ?>
        <textarea rows="4" cols="10" name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $address; ?></textarea>
    <?php else: ?>
        <?php echo $address; ?>
        <?php echo  CHtml::hiddenField("DocumentTarget[$row_index][attributes][address]", $address, array('data-rowindex' => $row_index)); ?>
    <?php endif; ?>
</div>