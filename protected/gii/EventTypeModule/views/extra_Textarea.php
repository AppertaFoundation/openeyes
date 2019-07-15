<div style="margin-top: 8px;">
    <table>
        <tr>
            <td>Rows:</td>
            <td><input type="text" class="returnnext" id="textAreaRows<?php echo $element_num?>Field<?php echo $field_num?>" name="textAreaRows<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo empty($_POST) ? '6' : @$_POST['textAreaRows'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['textAreaRows'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['textAreaRows'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
        <tr>
            <td>Columns:</td>
            <td><input type="text" class="noreturn" id="textAreaCols<?php echo $element_num?>Field<?php echo $field_num?>" name="textAreaCols<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo empty($_POST) ? '80' : @$_POST['textAreaCols'.$element_num.'Field'.$field_num]?>" /></td>
        </tr>
        <?php if (isset($this->form_errors['textAreaCols'.$element_num.'Field'.$field_num])) {
    ?>
            <tr>
                <td></td>
                <td>
                    <span style="color: #f00;"><?php echo $this->form_errors['textAreaCols'.$element_num.'Field'.$field_num]?></span>
                </td>
            </tr>
        <?php 
}?>
    </table>
</div>
