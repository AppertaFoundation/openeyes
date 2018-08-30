<?php
/**
 * @var $this TrialContext
 */
?>
<span class="js-add-remove-participant"
      data-patient-id="<?= $this->patient->id ?>"
      data-trial-id="<?= $this->trial->id ?>"
>
<a class="js-add-to-trial"
   style="<?= $this->isPatientInTrial() ? 'display:none;' : '' ?>"
>Add to trial</a>
<a class="js-remove-from-trial"
   style="<?= $this->isPatientInTrial() ? '' : 'display:none;' ?>"
>Remove from trial</a>
</span>

<script type="text/javascript">
    $(document).ready(function () {
        $('.js-add-remove-participant[data-patient-id=<?= $this->patient->id?>][data-trial-id=<?= $this->trial->id?>]')
            .each(function (index, element) {
                var addLink = $(element).find('.js-add-to-trial');
                var removeLink = $(element).find('.js-remove-from-trial');

                addLink.on("click", function addPatientToTrial(){
                    $.ajax({
                        url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
                        data: {
                            id: <?= $this->trial->id?>,
                            patient_id: <?= $this->patient->id?>,
                            YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()
                        },
                        type: 'POST',
                        success: function (response) {
                            addLink.hide();
                            removeLink.show();
                        },
                        error: function (response) {
                            new OpenEyes.UI.Dialog.Alert({
                                content: "Sorry, an internal error occurred and we were unable to add the patient to the trial.\n\nPlease contact support for assistance."
                            }).open();
                        }
                    });
                });

                removeLink.on("click", function addPatientToTrial() {
                    $.ajax({
                        url: '<?php echo Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
                        data: {
                            id: <?= $this->trial->id?>,
                            patient_id: <?= $this->patient->id?>,
                            YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()},
                        type: 'POST',
                        success: function (response) {
                            removeLink.hide();
                            addLink.show();
                        },
                        error: function (response) {
                            new OpenEyes.UI.Dialog.Alert({
                                content: "Sorry, an internal error occurred and we were unable to remove the patient from the trial.\n\nPlease contact support for assistance."
                            }).open();
                        }
                    });
                    }
                );

            });
    });
</script>
