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

function getMacroName($doc_instance_id)
{
    $doc_instance_version = DocumentInstanceData::model()->findByAttributes(array('document_instance_id'=>$doc_instance_id));
    if($doc_instance_version->macro_id > 0)
    {
        $macro = LetterMacro::model()->findByPk($doc_instance_version->macro_id);
        return $macro->name;
    }
    return '';
}

function getCorrespondenceId($doc_instance_id)
{
    $doc_instance = DocumentInstance::model()->findByAttributes(array('id'=>$doc_instance_id));
    return $doc_instance->correspondence_event_id;
}

?>
    <table id="dm_table">
    <tbody>
    <tr id="dm_0">
        <th>To/CC</th>
        <th>Recipient/Address</th>
        <th>Role</th>
        <th>Delivery Method(s)</th>
        <th>Actions</th>
    </tr>
    <?php

        if(isset($data["doctargets"])) {
            foreach($data["doctargets"] as $doc_target) {
                $row_index = 0;
                foreach($data["docoutputs"] as $doc_output){
                    if ($doc_output["document_target_id"] == $doc_target["id"]) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $doc_output["ToCc"] ?>
                            </td>
                            <td>
                                <?php echo $doc_target["contact_name"] ?>
                                <br>
                                <div id="docman_address_<?php echo $doc_target["id"]?>"><?php echo $doc_target["address"]; ?></div>
                                <br>
                                <a id="docman_edit_button_<?php echo $doc_target["id"]?>" onClick="docman2.editAddress(<?php echo $doc_target["id"]?>);">Edit Address</a>
                            </td>
                            <td>
                                <?php echo ucfirst($doc_target["contact_type"]);?>
                                <?php if($doc_target["contact_modified"]){ echo "<br>(Modified)";}?>
                            </td>
                            <td>
                                <?php echo ucfirst($doc_output["output_type"]).' - '.ucfirst($doc_output["output_status"]);?>
                            </td>
                            <td>
                                <?php
                                if($doc_output["ToCc"] != "To")
                                {?>
                                    <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $row_index++;
                    }
                }
            }
        }
    // TODO: need to check if the user has proper rights to add new document!
    if(isset($data["correspondence_mode"]) && $data["correspondence_mode"]){
        $this->renderPartial('/docman/document_row_edit', array('data'=>$data));
    ?>
    <?php
    }else{
    ?>
    <tr><td colspan="5"><button class="docman green" id="docman_add_new">Add New</button></td></tr>
    <?php
        }?>
    </tbody>
    </table>