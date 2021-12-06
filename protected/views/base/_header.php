<?php
/**
 * @var SiteController $this
 */

if (!isset($form_params)) {
    $form_params = array();
}

if (!isset($as_clinic)) {
    $as_clinic = false;
}
?>

<header class="oe-header">
    <?php if ($this->action->id === 'login') : ?>
      <div class="oe-logo-flag-help">
        <i class="oe-i direction-left pro-theme no-click"></i> Click for Help, Theme change, Tours &amp; Feedback
      </div>
    <?php endif; ?>

    <?php if ($this->renderPatientPanel === true) {
        $this->renderPartial('//patient/_patient_id');
    } ?>

    <?php $this->renderPartial('//base/_form', ['as_clinic' => $as_clinic]); ?>
</header>