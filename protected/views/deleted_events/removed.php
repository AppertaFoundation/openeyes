<div class="removed-record-watermark">Removed record - Do not use</div>
<?php
    $this->beginContent('//patient/event_container', array('no_face' => false));
    $clinical = $this->checkAccess('OprnViewClinical');
    $requested_by = 'N/A';
    $requested_date = 'N/A';
if ($event_previous_version) {
    $requested_by = $event_previous_version->usermodified->getFullName();
    $requested_date = Helper::convertDate2NHS($event_previous_version->last_modified_date);
}
    $completed_by = $this->event->usermodified->getFullName();
    $completed_date = Helper::convertDate2NHS($this->event->last_modified_date);
    $reson = $this->event->delete_reason;

?>
<div class="alert-box warning">
    <h3>Deletion</h3>
    <ul>
        <li><small>Request by:</small> <?=$requested_by?></li>
        <li><small>Requested date:</small> <?=$requested_date?></li>
        <li><small>Completed by:</small> <?=$completed_by?></li>
        <li><small>Completed date:</small> <?=$completed_date?></li>
        <li><small>Reason:</small> <em><?=$reson?></em></li>
    </ul>
    
</div>
<?php
    $this->event_actions[] = EventAction::printButton();
    $this->renderOpenElements('view');
    $this->endContent();
?>