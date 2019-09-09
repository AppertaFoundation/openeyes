<div style="margin-top: 8px;">
    <table>
        <tr>
            <td>Min value:</td>
            <td><input type="text" class="returnnext" id="integerMinValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerMinValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerMinValue'.$element_num.'Field'.$field_num]?>" /> (eg 1)</td>
        </tr>
        <?php if (isset($this->form_errors['integerMinValue'.$element_num.'Field'.$field_num])) {
            ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['integerMinValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
            <?php
        }?>
        <tr>
            <td>Max value:</td>
            <td><input type="text" class="returnnext" id="integerMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerMaxValue'.$element_num.'Field'.$field_num]?>" /> (eg 100 or 133.7)</td>
        </tr>
        <?php if (isset($this->form_errors['integerMaxValue'.$element_num.'Field'.$field_num])) {
            ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['integerMaxValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
            <?php
        }?>
        <tr>
            <td>Default value:</td>
            <td><input type="text" class="returnnext" id="integerDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerDefaultValue'.$element_num.'Field'.$field_num]?>" /> (eg 10.7, blank for none)</td>
        </tr>
        <?php if (isset($this->form_errors['integerDefaultValue'.$element_num.'Field'.$field_num])) {
            ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['integerDefaultValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
            <?php
        }?>
        <tr>
            <td>Field size:</td>
            <td><input type="text" class="returnnext" id="integerSize<?php echo $element_num?>Field<?php echo $field_num?>" name="integerSize<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo empty($_POST) ? '10' : @$_POST['integerSize'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['integerSize'.$element_num.'Field'.$field_num])) {
            ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['integerSize'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
            <?php
        }?>
        <tr>
            <td>Max length:</td>
            <td><input type="text" class="noreturn" id="integerMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" name="integerMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerMaxLength'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['integerMaxLength'.$element_num.'Field'.$field_num])) {
            ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['integerMaxLength'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
            <?php
        }?>
    </table>
</div>
