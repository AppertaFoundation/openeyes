<h1>Event type module Generator</h1>

<p>This generator helps you to generate the skeleton code needed by an OpenEyes event type module.</p>
fish
<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<h3>Describe your event type:</h3>
		<label>Specialty: </label><?php echo CHtml::dropDownList('Specialty[id]',@$_REQUEST['Specialty']['id'], CHtml::listData(Specialty::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br />
		<label>Event group: </label><?php echo CHtml::dropDownList('EventGroup[id]', @$_REQUEST['EventGroup']['id'], CHtml::listData(EventGroup::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br />
		<label>Name of event type: </label> <?php echo $form->textField($model,'moduleSuffix',array('size'=>65)); ?><br />

		<h3>Describe your element types:</h3>

		<div class="giiElementContainer">
			<div class="giiElement" style="background:#eee;border:1px solid #999;padding:5px;">
				<h4><?php echo CHtml::textField('elementName1','Test element',array('size'=>35, 'style'=>'font-size: 16px;')); ?></h4>
				<label>Field: </label><?php echo CHtml::textField('elementName1FieldName1','',array('size'=>35)); ?> 

<select name="elementType1FieldType1">
		<option value="1">Textbox</option>
		<option value="1">Textarea</option>
		<option value="1">Date picker</option>
		<option value="1">Dropdown list</option>
		<option value="1">Checkboxes</option>
		<option value="1">Radio buttons</option>
		<option value="1">EyeDraw</option>
</select>

<input type="submit" name="add" value="add" /><br />

			</div>
		</div>
		<label>Element name: </label><?php echo CHtml::textField('elementName1','',array('size'=>65)); ?> <input type="submit" name="add" value="add" /><br />

		<div class="tooltip">
			The name should only contain word characters and spaces.  The generated module class will be named based on the specialty, event group, and name of the event type.  EG: 'Ophthalmology', 'Treatment', and 'Operation note' will take the short codes for the specialty and event group to create <code>OphTrOperationnote</code>.
		</div>
		<?php echo $form->error($model,'moduleID'); ?>
	</div>
.
<?php $this->endWidget(); ?>
