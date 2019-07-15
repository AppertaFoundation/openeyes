<div style="margin-top: 8px;">
    <table>
        <tr>
            <td>Min value:</td>
            <td><input type="text" class="returnnext" id="sliderMinValue<?php echo $element_num?>Field<?php echo $field_num?>" name="sliderMinValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['sliderMinValue'.$element_num.'Field'.$field_num]?>" /> (eg 1)</td>
        </tr>
        <?php if (isset($this->form_errors['sliderMinValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['sliderMinValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Max value:</td>
            <td><input type="text" class="returnnext" id="sliderMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" name="sliderMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['sliderMaxValue'.$element_num.'Field'.$field_num]?>" /> (eg 100 or 133.7)</td>
        </tr>
        <?php if (isset($this->form_errors['sliderMaxValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['sliderMaxValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Default value:</td>
            <td><input type="text" class="returnnext" id="sliderDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" name="sliderDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['sliderDefaultValue'.$element_num.'Field'.$field_num]?>" /> (eg 10.7, blank for none)</td>
        </tr>
        <?php if (isset($this->form_errors['sliderDefaultValue'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['sliderDefaultValue'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Stepping:</td>
            <td><input type="text" class="returnnext" id="sliderStepping<?php echo $element_num?>Field<?php echo $field_num?>" name="sliderStepping<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['sliderStepping'.$element_num.'Field'.$field_num]?>" /> (eg 1 or 0.25)</td>
        </tr>
        <?php if (isset($this->form_errors['sliderStepping'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['sliderStepping'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Decimal points:</td>
            <td><input type="text" class="noreturn" id="sliderForceDP<?php echo $element_num?>Field<?php echo $field_num?>" name="sliderForceDP<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['sliderForceDP'.$element_num.'Field'.$field_num]?>" /> (eg 2 to force values to have two decimal points)</td>
        </tr>
        <?php if (isset($this->form_errors['sliderForceDP'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['sliderForceDP'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
    </table>
</div>
