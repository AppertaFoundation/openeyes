<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 26/05/2017
 * Time: 4:21 PM
 */

/**
 * @var $form CActiveForm
 */
?>

<?php $form = $this->beginWidget('CCodeForm', array('model' => $model)); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'className'); ?>
        <?php echo $form->textField($model, 'className', array('size' => 65, 'id' => 'class-name')); ?>
        <div class="tooltip">
            Variable class name must only contain word characters.
        </div>
        <?php echo $form->error($model, 'className'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 65, 'id' => 'name')); ?>
        <div class="tooltip">
            This value is used for lookup.
        </div>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'label'); ?>
        <?php echo $form->textField($model, 'label', array('size' => 65, 'id' => 'label')); ?>
        <div class="tooltip">
            This value is displayed on-screen.
        </div>
        <?php echo $form->error($model, 'label'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'unit'); ?>
        <?php echo $form->textField($model, 'unit', array('size' => 20, 'id' => 'label')); ?>
        <div class="tooltip">
            This value is displayed as part of the x-axis of a Plotly graph.
        </div>
        <?php echo $form->error($model, 'unit'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'searchProviders'); ?>
        <?php echo $form->textField($model, 'searchProviders', array('size' => 65)); ?>
        <div class="tooltip">
            At least one search provider must be listed here. Separate each search provider with a comma.
        </div>
        <?php echo $form->error($model, 'searchProviders'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'eyeCardinality'); ?>
        <?php echo $form->checkBox($model, 'eyeCardinality'); ?>
        <div class="tooltip">
            At least one search provider must be listed here. Separate each search provider with a comma.
        </div>
        <?php echo $form->error($model, 'searchProviders'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model, 'path'); ?>
        <?php echo $form->textField($model, 'path', array('size' => 65)); ?>
        <div class="tooltip">
            This refers to the module that the new model class and test case should be generated under.
            It should be specified in the form of a path alias, for example, <code>application.modules.OphCiExamination</code>.
            Alternatively, you can specify <code>application</code> here to place the code at the application level.
        </div>
        <?php echo $form->error($model, 'path'); ?>

    </div>

<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScript('VariableAutoComplete', '
$("#class-name").bind("keyup change", function() {
  var name = $("#name");
  var label = $("#label");
  if (!name.data("changed")) {
    var text = $("#class-name").val();
    var output = $("#class-name").val().charAt(0).toLowerCase();
    var character = "";
    for (i = 1; i < text.length; i++) {
      character = text.charAt(i);
      output = output.concat(character.toLowerCase());
    }
    name.val(output);
  }
  if (!label.data("changed")) {
    var text = $("#class-name").val();
    var output = $("#class-name").val().charAt(0);
    var character = "";
    for (i = 1; i < text.length; i++) {
      character = text.charAt(i);
      output = output.concat(character);
    }
    label.val(output);
  }
});
');
