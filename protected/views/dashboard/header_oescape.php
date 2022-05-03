<?php
$institution_id = Institution::model()->getCurrent()->id;
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
    SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
    $this->patient->id,
    $institution_id,
    Yii::app()->session['selected_site_id']
);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
    SettingMetadata::model()->getSetting('display_secondary_number_usage_code'),
    $this->patient->id,
    $institution_id,
    Yii::app()->session['selected_site_id']
);
?>

<div class="mdl-layout__header-row">
    <div class="openeyes-logo">
        <img src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/OpenEyes_logo_transparent.png')?>" alt="OpenEyes logo"/>
    </div>
    <span class="mdl-layout-title">
        <?php
            echo $this->patient->getFullName();
        ?>
    </span>
    <span>(<?php echo $this->patient->getAge(); ?>)</span>
    <span class="header-icon mdi
        <?php
        if ($this->patient->gender == 'F') {
            echo 'mdi-human-female';
        } elseif ($this->patient->gender == 'M') {
            echo 'mdi-human-male';
        }?>">
    </span>
    <span class="header-icon mdi
        <?php
        if ($this->patient->getOphInfo()->cvi_status_id == 1 || $this->patient->getOphInfo()->cvi_status_id == 2) {
            echo 'mdi-eye';
        } else {
            echo 'mdi-eye-off';
        } ?>">
    </span>
    <section class="patient-details">
        <span class="nhs-number">
            <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?>
        </span>
        <span><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>:
            <b><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></b>
        </span>
        <span>
            <?php echo $this->patient->getAllergiesString(); ?>
        </span>
    </section>
    <div class="mdl-layout-spacer"></div>
    <b>Glaucoma</b>
</div>
