<?php
$form_id = 'lab-results-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
));

// Event actions
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));

$this->displayErrors($errors)?>

<?php $this->renderOpenElements($this->action->id, $form); ?>

<?php $this->displayErrors($errors, true)?>
<?php $this->endWidget(); ?>

    <div id="dialog-confirm-cancel" title="Cancel" class="hidden">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>All text entered will be lost. Are you sure?</p>
    </div>

<?php $this->endContent();?>