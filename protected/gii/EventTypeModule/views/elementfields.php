<?php
// collate field numbers
$field_nums = array();
foreach ($_POST as $key => $value) {
    if (preg_match('/^elementName'.$element_num.'FieldName([0-9]+)$/', $key, $m)) {
        $field_nums[] = $m[1];
    }
}
sort($field_nums);

if (isset($_GET['event_type_id'])) {
    $event_type_id = $_GET['event_type_id'];
} else {
    $event_type_id = @$_REQUEST['EventTypeModuleEventType'];
}
?>
<div class="giiElementContainer" style="margin-bottom: 10px;">
	<div class="giiElement" style="background:#eee;border:1px solid #999;padding:5px;">
		<label>Select the element to add fields to</label>
		<h4 style="margin-bottom: 0;">
			<select class="elementToAddFieldsTo" name="elementId<?php echo $element_num?>">
				<option value="">Select</option>
				<?php foreach (ElementType::model()->findAll('event_type_id=?', array($event_type_id)) as $element_type) {
    ?>
					<option value="<?php echo $element_type->id?>"<?php if (@$_POST['elementId'.$element_num] == $element_type->id) {
    ?> selected="selected"<?php 
}
    ?>><?php echo $element_type->name?></option>
				<?php 
}?>
			</select>
		</h4>
		<div class="element_fields" style="margin-top: 2em;">
			<?php foreach ($field_nums as $field_num) {
    echo $this->renderPartial('element_field', array('element_num' => $element_num, 'field_num' => $field_num));
}
            ?>
		</div>
		<div style="float: right">
			<input type="submit" class="remove_element" name="removeElement<?php echo $element_num?>" value="remove element" />
		</div>
		<input type="submit" class="add_element_field" name="addElementField<?php echo $element_num?>" value="add field"<?php if (!@$_POST['elementId'.$element_num]) {
    ?> style="display: none;"<?php 
}?> /><br />
	</div>
</div>
