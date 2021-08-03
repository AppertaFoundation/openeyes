<?php
$form_id = 'document-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
       ),
));

// Event actions
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));

$this->displayErrors($errors)?>

<?php $this->renderOpenElements($this->action->id, $form); ?>

<?php $this->displayErrors($errors, true)?>
<?php $this->endWidget(); ?>

<?php $this->endContent();
