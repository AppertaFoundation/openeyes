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
    <span class="icon-element
        <?php
            if($this->patient->gender == "F"){
                echo "icon-female";
            }else if($this->patient->gender == "M"){
                echo "icon-male";
            }?>">
    </span>
    <span class="icon-element
        <?php
            if($this->patient->getOphInfo()->cvi_status_id == 1 || $this->patient->getOphInfo()->cvi_status_id == 2 ){
                echo "icon-eye";
            }else{
                echo "icon-eye-impaired";
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

