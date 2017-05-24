<header class="header row">
    
    <!-- Branding (logo) -->
    <div class="large-2 column">
        <?php $this->renderPartial('//base/_brand'); ?>
    </div>

    <!-- Patient panel -->
    <div class="large-4 medium-5 column">
        <?php if ($this->renderPatientPanel === true) {
            $this->renderPartial('//patient/_patient_id');
        }?>
    </div>

    <!-- User panel (with site navigation) -->
    <div class="large-6 medium-7 column">
        <?php $this->renderPartial('//base/_form'); ?>
    </div>
</header><!-- /.header -->