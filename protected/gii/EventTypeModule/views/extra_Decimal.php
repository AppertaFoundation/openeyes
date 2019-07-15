<div style="margin-top: 8px;">
    <table>
        <tr>
            <td>Min value:</td>
            <td><input type="text" class="returnnext decimalMinValue" id="decimalMinValue<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalMinValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['decimalMinValue'.$element_num.'Field'.$field_num]?>" /> (eg 1)</td>
        </tr>
        <?php if (isset($this->form_errors['decimalMinValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalMinValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Max value:</td>
            <td><input type="text" class="returnnext decimalMaxValue" id="decimalMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['decimalMaxValue'.$element_num.'Field'.$field_num]?>" /> (eg 100 or 133.7)</td>
        </tr>
        <?php if (isset($this->form_errors['decimalMaxValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalMaxValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Default value:</td>
            <td><input type="text" class="returnnext" id="decimalDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['decimalDefaultValue'.$element_num.'Field'.$field_num]?>" /> (eg 10.7, blank for none)</td>
        </tr>
        <?php if (isset($this->form_errors['decimalDefaultValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalDefaultValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Decimal points:</td>
            <td><input type="text" class="noreturn decimalForceDP" id="decimalForceDP<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalForceDP<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['decimalForceDP'.$element_num.'Field'.$field_num]?>" /> (eg 2 to force values to have two decimal points)</td>
        </tr>
        <?php if (isset($this->form_errors['decimalForceDP'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalForceDP'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Field size:</td>
            <td><input type="text" class="returnnext" id="decimalSize<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalSize<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo empty($_POST) ? '10' : @$_POST['decimalSize'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['decimalSize'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalSize'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Max length:</td>
            <td><input type="text" class="noreturn" id="decimalMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" name="decimalMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['decimalMaxLength'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['decimalMaxLength'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['decimalMaxLength'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
    </table>
</div>
