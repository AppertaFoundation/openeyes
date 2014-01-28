<div id="radioButtonFieldSQLDetails<?php echo $element_num?>Field<?php echo $field_num?>">
	Table:
	<select name="radioButtonFieldSQLTable<?php echo $element_num?>Field<?php echo $field_num?>" class="radioButtonFieldSQLTable">
		<option value="">- Please select a table -</option>
		<?php foreach (Yii::app()->getDb()->getSchema()->getTableNames() as $table) {?>
			<option value="<?php echo $table?>"<?php if (@$_POST['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num] == $table) {?> selected="selected"<?php }?>><?php echo $table?></option>
		<?php }?>
	</select>&nbsp;<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" /><br/>
	<?php if (isset($this->form_errors['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div id="radioButtonFieldSQLTableFieldDiv<?php echo $element_num?>Field<?php echo $field_num?>"<?php if (!@$_POST['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num]) {?> style="display: none;"<?php }?>>
		Field: <select name="radioButtonFieldSQLTableField<?php echo $element_num?>Field<?php echo $field_num?>" class="radioButtonFieldSQLTableField">
			<?php if (@$_POST['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num]) {?>
				<?php EventTypeModuleCode::dump_table_fields(@$_POST['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num],@$_POST['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num])?>
			<?php }?>
		</select><br/>
		<?php if (isset($this->form_errors['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num])) {?>
			<span style="color: #f00;"><?php echo $this->form_errors['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num]?></span><br/>
		<?php }?>
		<div id="radioButtonFieldSQLTableDefaultValueDiv<?php echo $element_num?>Field<?php echo $field_num?>"<?php if (!@$_POST['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num]) {?> style="display: none;"<?php }?>>
			Default value: <select name="radioButtonFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>">
				<?php if (@$_POST['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num]) {?>
					<?php EventTypeModuleCode::dump_field_unique_values(@$_POST['radioButtonFieldSQLTable'.$element_num.'Field'.$field_num],@$_POST['radioButtonFieldSQLTableField'.$element_num.'Field'.$field_num],@$_POST['radioButtonFieldValueTextInputDefault'.$element_num.'Field'.$field_num])?>
				<?php }?>
			</select><br/>
		</div>
	</div>
</div>
