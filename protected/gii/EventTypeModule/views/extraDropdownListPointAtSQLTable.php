<div id="dropDownFieldSQLDetails<?php echo $element_num?>Field<?php echo $field_num?>">
	Table:
	<select name="dropDownFieldSQLTable<?php echo $element_num?>Field<?php echo $field_num?>" class="dropDownFieldSQLTable">
		<option value="">- Please select a table -</option>
		<?php foreach (Yii::app()->getDb()->getSchema()->getTableNames() as $table) {?>
			<option value="<?php echo $table?>"<?php if (@$_POST['dropDownFieldSQLTable'.$element_num.'Field'.$field_num] == $table) {?> selected="selected"<?php }?>><?php echo $table?></option>
		<?php }?>
	</select>&nbsp;<img src="/img/ajax-loader.gif" class="loader" alt="loading..." style="display: none;" /><br/>
	<div id="dropDownFieldSQLTableFieldDiv<?php echo $element_num?>Field<?php echo $field_num?>"<?php if (!@$_POST['dropDownFieldSQLTable'.$element_num.'Field'.$field_num]) {?> style="display: none;"<?php }?>>
		Field: <select name="dropDownFieldSQLTableField<?php echo $element_num?>Field<?php echo $field_num?>">
			<?php if (@$_POST['dropDownFieldSQLTable'.$element_num.'Field'.$field_num]) {?>
				<?php EventTypeModuleCode::dump_table_fields(@$_POST['dropDownFieldSQLTable'.$element_num.'Field'.$field_num],@$_POST['dropDownFieldSQLTableField'.$element_num.'Field'.$field_num])?>
			<?php }?>
		</select>
	</div>
</div>
