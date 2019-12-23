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
            <?php echo $this->patient->getNhsnum(); ?>
        </span>
        <span>Hospital No:
            <b><?php echo $this->patient->hos_num; ?></b>
        </span>
        <span>
            <?php echo $this->patient->getAllergiesString(); ?>
        </span>
    </section>
    <div class="mdl-layout-spacer"></div>
    <b>Glaucoma</b>
</div>

