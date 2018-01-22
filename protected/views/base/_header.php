
<header class="oe-header">
    <?php if ($this->renderPatientPanel === true) {
        $this->renderPartial('//patient/_patient_id');
    }?>

    <?php $this->renderPartial('//base/_form'); ?>
</header><!-- /.header -->