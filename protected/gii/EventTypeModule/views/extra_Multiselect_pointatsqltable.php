<div id="multiSelectFieldSQLDetails<?php echo $element_num?>Field<?php echo $field_num?>">
	Table:
	<select name="multiSelectFieldSQLTable<?php echo $element_num?>Field<?php echo $field_num?>" class="multiSelectFieldSQLTable">
		<option value="">- Please select a table -</option>
		<?php foreach (Yii::app()->getDb()->getSchema()->getTableNames() as $table) {?>
			<option value="<?php echo $table?>"<?php if (@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num] == $table) {?> selected="selected"<?php }?>><?php echo $table?></option>
		<?php }?>
	</select>&nbsp;<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" /><br/>
	<?php if (isset($this->form_errors['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div id="multiSelectFieldSQLTableFieldDiv<?php echo $element_num?>Field<?php echo $field_num?>"<?php if (!@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num]) {?> style="display: none;"<?php }?>>
		Field: <select name="multiSelectFieldSQLTableField<?php echo $element_num?>Field<?php echo $field_num?>" class="multiSelectFieldSQLTableField">
			<?php if (@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num]) {?>
				<?php EventTypeModuleCode::dump_table_fields(@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num],@$_POST['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num])?>
			<?php }?>
		</select><br/>
		<?php if (isset($this->form_errors['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num])) {?>
			<span style="color: #f00;"><?php echo $this->form_errors['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num]?></span><br/>
		<?php }?>
		<div id="multiSelectFieldSQLTableDefaultValueDiv<?php echo $element_num?>Field<?php echo $field_num?>"<?php if (!@$_POST['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num]) {?> style="display: none;"<?php }?>>
			Default values: <select class="multiSelectFieldValueDefault" name="multiSelectFieldValueDefault<?php echo $element_num?>Field<?php echo $field_num?>">
				<?php if (@$_POST['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num]) {?>
					<?php EventTypeModuleCode::dump_field_unique_values_multi(@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num],@$_POST['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num],@$_POST['multiSelectFieldValueDefaults'.$element_num.'Field'.$field_num])?>
				<?php }?>
			</select><br/>
			<div id="multiSelectFieldValueDefaultsDiv<?php echo $element_num?>Field<?php echo $field_num?>" style="margin-top: 5px;">
				<?php if (@$_POST['multiSelectFieldValueDefaults'.$element_num.'Field'.$field_num]) {
					$model = EventTypeModuleCode::findModelClassForTable(@$_POST['multiSelectFieldSQLTable'.$element_num.'Field'.$field_num]);
					$field = @$_POST['multiSelectFieldSQLTableField'.$element_num.'Field'.$field_num];

					foreach (@$_POST['multiSelectFieldValueDefaults'.$element_num.'Field'.$field_num] as $value) {
						$item = $model::model()->findByPk($value);?>
				<div><input type="hidden" name="multiSelectFieldValueDefaults<?php echo $element_num?>Field<?php echo $field_num?>[]" value="<?php echo $item->id?>" /><span><?php echo $item->$field?></span> <a href="#" class="multiSelectFieldValueDefaultsRemove">(remove)</a></div>
					<?php }
				}?>
			</div>
		</div>
	</div>
</div>
