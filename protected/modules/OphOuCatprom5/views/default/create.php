<?php
$form_id = 'catprom5-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 10,
    ),
));
?>

<?php $this->displayErrors($errors) ?>
<?php $this->renderOpenElements($this->action->id, $form); ?>
<?php $this->displayErrors($errors, true) ?>

<?php $this->endWidget(); ?>
<?php $this->endContent();
