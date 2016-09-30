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

$row_index = 0;
?>
    <tr class="new_entry_row" <?php if(isset($data['macro_id'])){?> data-rowindex="<?php echo $row_index;?>" <?php }?>>
        <?php
        $row_count = 1;

        if(!isset($element))
        {
            $element = new ElementLetter();
        }

        if(isset($data["cc"]))
        {
            $row_count += count($data["cc"]);
        }
        ?>

    <td>
        <?php
            if(isset($data['macro_id'])){
                $macro_id = $data['macro_id'];
            }else
            {
                $macro_id = null;
            }
            echo CHtml::dropDownList('macro_id',  $macro_id, $this->getMacros(), array('empty' => '- Macro -', 'nowrapper' => true, 'class' => 'full-width',  'data-rowindex'=>$row_index));
        ?>
    </td>
    <?php
        if(isset($data["to"]))
        { ?>
            <td>
                <?php echo CHtml::dropDownList('target_type[]', 'To', array('To'=>'To','CC'=>'CC'), array('empty' => '- To/CC -', 'nowrapper' => true, 'class' => 'full-width', 'data-rowindex'=>$row_index));?>
            </td>
            <td>
                <?php echo CHtml::dropDownList('contact_id[]', $data["to"]["contact_id"], $element->address_targets,  array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index))?>
                <br>
                <textarea rows="4" cols="10" name="address[]" id="address_<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>"><?php echo $data["to"]["address"]?></textarea>
            </td>
            <td>
                <?php echo CHtml::dropDownList('contact_type[]', $data["to"]["contact_type"], array('Gp'=>'Gp','Patient'=>'Patient', 'DRSS'=>'DRSS', 'Legacy'=>'Legacy', 'Other'=>'Other'), array('empty' => '- Type -', 'nowrapper' => true, 'class' => 'full-width docman_contact_type', 'id'=>'contact_type_'.$row_index, 'data-rowindex'=>$row_index));?>
            </td>
            <td class="docman_delivery_method">
                <label><input type="checkbox" name="print[]"  <?php if($data["to"]["contact_type"] != 'Gp'){ echo 'checked';}?>>Print</label><br>
                <?php if($data["to"]["contact_type"] == 'Gp'){?>
                    <label><input type="checkbox" class="docman_delivery" name="docman[]" data-rowindex="<?php echo $row_index?>" checked>DocMan</label><br>
                <?php }?>
                <!--<label><input type="checkbox" name="to_email" disabled>Email</label>!-->
            </td>
            <td>
                <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
            </td>
        <?php
        $row_index++;
        }
    ?>
    </tr>
    <?php
    if(isset($data["cc"])){
        foreach($data["cc"] as $row){
            //var_dump($row);
        ?>
            <tr class="new_entry_row" data-rowindex="<?php echo $row_index ?>">
                <td></td>
                <td>
                    <?php echo CHtml::dropDownList('target_type[]', 'CC', array('To'=>'To','CC'=>'CC'), array('empty' => '- To/CC -', 'nowrapper' => true, 'class' => 'full-width', 'data-rowindex'=>$row_index));?>
                </td>
                <td>
                    <?php echo CHtml::dropDownList('contact_id[]', $row["contact_id"], $element->address_targets, array('empty' => '- CC -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index));?>
                    <br>
                    <textarea rows="4" cols="10" name="address[]" id="address_<?php echo $row_index ?>" data-rowindex="<?php echo $row_index?>"><?php echo $row["address"]?></textarea>
                </td>
                <td>
                    <?php echo CHtml::dropDownList('contact_type[]', $row["contact_type"], array('Gp'=>'Gp','Patient'=>'Patient', 'DRSS'=>'DRSS', 'Legacy'=>'Legacy', 'Other'=>'Other'), array('empty' => '- Type -', 'nowrapper' => true, 'class' => 'full-width docman_contact_type', 'id'=>'contact_type_'.$row_index, 'data-rowindex'=>$row_index));?>
                </td>
                <td class="docman_delivery_method">
                    <label><input type="checkbox" name="print[]" <?php if($row["contact_type"] != 'Gp'){ echo 'checked';}?>>Print</label><br>
                    <?php if($row["contact_type"] == 'Gp'){?>
                        <label><input type="checkbox" class="docman_delivery" name="docman[]" data-rowindex="<?php echo $row_index?>" checked>DocMan</label><br>
                    <?php }?>
                    <!--<label><input type="checkbox" name="cc_email[]" disabled>Email</label>!-->
                </td>
                <td>
                    <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
                </td>
            </tr>
        <?php
        $row_index++;
        }
    }

    ?>
    <tr class="new_entry_row">
        <td colspan="6">
            <button class="button small secondary" id="docman_add_new_recipient">Add new recipient</button>
        </td>
    </tr>
