<?php
// collate field numbers
$field_nums = array();
foreach ($_POST as $key => $value) {
    if (preg_match('/^elementName'.$element_num.'FieldName([0-9]+)$/', $key, $m)) {
        $field_nums[] = $m[1];
    }
}
sort($field_nums);
?>
<div class="giiElementContainer" style="margin-bottom: 10px;">
    <div class="giiElement" style="background:#eee;border:1px solid #999;padding:5px;">
        <label>Enter a name for the element</label>
        <h4 style="margin-bottom: 0;"><?=\CHtml::textField('elementName'.$element_num, @$_POST['elementName'.$element_num], array('size' => 35, 'style' => 'font-size: 16px;', 'class' => 'elementNameTextField')); ?></h4>
        <?php if (isset($this->form_errors['elementName'.$element_num])) {
    ?>
            <span style="color: #f00;"><?php echo $this->form_errors['elementName'.$element_num]?></span>
        <?php 
}?>
        <label>Element short name (used for table names)</label>
        <h4 style="margin-bottom: 0;"><?=\CHtml::textField('elementShortName'.$element_num, @$_POST['elementShortName'.$element_num], array('size' => 35, 'style' => 'font-size: 16px;', 'class' => 'elementShortNameTextField')); ?></h4>
        <?php if (isset($this->form_errors['elementShortName'.$element_num])) {
    ?>
            <span style="color: #f00;"><?php echo $this->form_errors['elementShortName'.$element_num]?></span>
        <?php 
}?>
        <div class="element_fields" style="margin-top: 2em;">
            <?php foreach ($field_nums as $field_num) {
    echo $this->renderPartial('element_field', array('element_num' => $element_num, 'field_num' => $field_num));
}
            ?>
        </div>
        <div style="float: right">
            <input type="submit" class="remove_element" name="removeElement<?php echo $element_num?>" value="remove element" />
        </div>
        <input type="submit" class="add_element_field" name="addElementField<?php echo $element_num?>" value="add field" /><br />
    </div>
</div>
