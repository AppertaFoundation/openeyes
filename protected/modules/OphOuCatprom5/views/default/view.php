<?php
$this->beginContent('//patient/event_container', array('no_face'=>false));?>

<?php
// Event actions
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}
?>

<?php if ($this->event->delete_pending) { ?>
    <div class="alert-box alert with-icon">
        This event is pending deletion and has been locked.
    </div>
<?php } ?>

<?php $this->renderOpenElements($this->action->id)?>
<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent();?>