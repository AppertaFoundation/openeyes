<div style="margin-top: 8px;">
    Dropdown method:&nbsp;&nbsp;&nbsp;<input type="radio" class="dropDownMethodSelector" name="dropDownMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="0" <?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '0') {
        ?> checked="checked" <?php
                                                                                                             }?>/> Enter values
    &nbsp;&nbsp;
    <input type="radio" class="dropDownMethodSelector" name="dropDownMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="1" <?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '1') {
        ?> checked="checked" <?php
                                                                           }?>/> Point at SQL table<br/>
    <?php if (isset($this->form_errors['dropDownMethod'.$element_num.'Field'.$field_num])) {
        ?>
        <span style="color: #f00;"><?php echo $this->form_errors['dropDownMethod'.$element_num.'Field'.$field_num]?></span><br/>
        <?php
    }?>
    <div style="height: 0.4em;"></div>
    <input type="checkbox" name="dropDownUseEmpty<?php echo $element_num?>Field<?php echo $field_num?>" value="1"<?php if (empty($_POST) || @$_POST['dropDownUseEmpty'.$element_num.'Field'.$field_num]) {
        ?> checked="checked"<?php
                                                 }?> /> Have a "Select" option at the top with a blank value
    <div style="height: 0.4em;"></div>
    <div id="dropDownMethodFields<?php echo $element_num?>Field<?php echo $field_num?>">
        <?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '0') {
            $this->renderPartial('extra_Dropdownlist_entervalues', array('element_num' => $element_num, 'field_num' => $field_num));
        } elseif (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '1') {
            $this->renderPartial('extra_Dropdownlist_pointatsqltable', array('element_num' => $element_num, 'field_num' => $field_num));
        }?>
    </div>
</div>
