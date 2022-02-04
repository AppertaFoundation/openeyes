<?php $patientIdentifierType = $identifier->patientIdentifierType; ?>
<?= $patientIdentifierType->long_title ? $patientIdentifierType->long_title : $patientIdentifierType->short_title ?>
<?php if ($patientIdentifierType->value_display_prefix) { ?>
    <?= $identifier->patientIdentifierType->value_display_prefix ?>
<?php } ?>
<?= ' : ' . $identifier->value ?>
<?php if ($patientIdentifierType->value_display_suffix) { ?>
    <?= $identifier->patientIdentifierType->value_display_suffix ?>
<?php } ?>
</br>
