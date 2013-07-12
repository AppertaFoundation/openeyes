<?php
/**
 * ____________________________________________________________________________
 *
 * This file is part of OpenEyes.
 *
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author Bill Aylward <bill.aylward@openeyes.org.uk>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 * @version 0.9
 * Creation date: 27 December 2011
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 * @package Clinical
 */

// Register javascript to mirror changes in element name to table name
$class = get_class($model);
$subspecialtyCode = strtolower(Yii::app()->params['subspecialtyCode']);
Yii::app()->clientScript->registerScript('gii.model',"
$('#{$class}_tableName').change(function(){
	$(this).data('changed',$(this).val()!='');
});
$('#{$class}_elementName').bind('keyup change', function(){
	var model=$('#{$class}_tableName');
	var elementName=$(this).val();
	if (elementName.substring(elementName.length-1)!='*') {
		$('.form .row.model-class').show();
	} else {
		$('#{$class}_tableName').val('');
		$('.form .row.model-class').hide();
	}
	if (!model.data('changed')) {
		var i=elementName.lastIndexOf('.');
		if(i>=0)
			elementName=elementName.substring(i+1);
		var tablePrefix=$('#{$class}_tablePrefix').val();
		if(tablePrefix!='' && elementName.indexOf(tablePrefix)==0)
			elementName=elementName.substring(tablePrefix.length);
		var tableName='element_' + '{$subspecialtyCode}' + '_';
		if(elementName.length>0)
				tableName+=elementName.substring(0).toLowerCase();
		model.val(tableName);
	}
});
$('.form .row.model-class').toggle($('#{$class}_elementName').val().substring($('#{$class}_elementName').val().length-1)!='*');
");
?>
<h1>OpenEyes Element Generator</h1>

<p>This generator generates default code for a new element.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model));

// Migration script generation
if ($model->level == $model::CREATE_MIGRATION) {
	// Element name
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'elementName');
	echo $form->textField($model,'elementName', array('size'=>65));
	echo '
		<div class="tooltip">
			The root name of the new element
		</div>';
	echo $form->error($model,'elementName');
	echo '
	</div>
	';

	// Migration path
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'migrationPath');
	echo $form->textField($model,'migrationPath', array('size'=>65, 'value'=>'application.migrations'));
	echo '
	<div class="tooltip">
		The directory that the new migration scripts will be generated in.
		The default location for OpenEyes elements is <code>application.migrations</code>.
	</div>';
	echo $form->error($model,'migrationPath');
	echo '
	</div>
	';

	// Element table fields
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'elementFields');
	echo $form->textArea($model,'elementFields', array('style'=>'width: 344px; height: 150px;'));
	echo '
	<div class="tooltip">
		A text block describing the fields required for the element.
		In order to ensure RDMS independence, use the following datatypes</br></br>
		<table cellspacing="0" width="220">
			<thead>
				<tr>
					<th align="left" width="30%">type</th>
					<th align="left" width="60%">Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>pk</td>
					<td>a generic primary key type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>string</td>
					<td>string type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>text</td>
					<td>text type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>integer</td>
					<td>integer type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>float</td>
					<td>floating point number type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>decimal</td>
					<td>decimal numebr type type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>datetime</td>
					<td>datetime type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>timestamp</td>
					<td>timestamp type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>time</td>
					<td>time type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>date</td>
					<td>date type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>binary</td>
					<td>binary data type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>boolean</td>
					<td>boolean type</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>money</td>
					<td>money/currency type</td>
				</tr>
			</tbody>
		</table>
	</div>';
	echo $form->error($model,'elementFields');
	echo '
	</div>
	';

	// Table name
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'tableName');
	echo $form->textField($model,'tableName', array('size'=>65));
	echo '
		<div class="tooltip">
			The table name that a new element model should be generated for
			(e.g. <code>element_oph_example</code>).
		</div>';
	echo $form->error($model,'tableName');
	echo '
	</div>
	';
}

// Model and view files creation
if ($model->level == $model::CREATE_FILES) {
	// Reset button
	echo '
	<div class="buttons">
		<input name="reset" type="submit" title="Click in case of error to start again" value="Reset" />
	</div>
	';

	// Class name
	echo '
	<div class="row model-class">
	';
	echo "\t".$form->label($model,'modelClass',array('required'=>true));
	echo $form->textField($model,'modelClass', array('size'=>65));
	echo '
		<div class="tooltip">
			The name of the model class to be generated (e.g. <code>ElementOphExample</code>).
		</div>';
	echo $form->error($model,'modelClass');
	echo '
	</div>
	';

	// Base class
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'baseClass');
	echo $form->textField($model,'baseClass',array('size'=>65));
	echo '
		<div class="tooltip">
			The name of the super class. The default super class for
			OpenEyes elements is <code>BaseElement</code>.
		</div>';
	echo $form->error($model,'baseClass');
	echo '
	</div>
	';

	// Model path
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'modelPath');
	echo $form->textField($model,'modelPath', array('size'=>65));
	echo '
		<div class="tooltip">
			The directory that the new model class file should be generated in.
			The default location for OpenEyes elements is <code>application.models.elements</code>.
		</div>';
	echo $form->error($model,'modelPath');
	echo '
	</div>
	';

	// Controller class
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'controllerClass');
	echo $form->textField($model,'controllerClass',array('size'=>65));
	echo '
		<div class="tooltip">
			CRUD controllers should be named after
			the model class name that they are dealing with
		</div>';
	echo $form->error($model,'controllerClass');
	echo '
	</div>
	';

	//Base controller class
	echo '
	<div class="row">
	';
	echo "\t".$form->labelEx($model,'baseControllerClass');
	echo $form->textField($model,'baseControllerClass',array('size'=>65));
	echo '
		<div class="tooltip">
			This is the class that the new CRUD controller class will extend from.
			Please make sure the class exists and can be autoloaded.
		</div>';
	echo $form->error($model,'baseControllerClass');
	echo '
	</div>
	';
}
?>
<?php $this->endWidget(); ?>

<!-- Script to put initial focus on element name -->
<script type="text/javascript">
	if ($('#ElementCode_elementName').val() == '') {
		$('#ElementCode_elementName').focus();
	}
</script>
