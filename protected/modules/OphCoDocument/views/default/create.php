<?php $this->beginContent('//patient/event_container'); ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'document-create',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
       ),
));

// Event actions
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => 'document-create'));

$this->displayErrors($errors)?>

<?php $this->renderOpenElements($this->action->id, $form); ?>

<?php $this->displayErrors($errors, true)?>
<?php $this->endWidget(); ?>

<?php $this->endContent();?>