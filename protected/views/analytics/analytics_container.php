

<?php $this->renderPartial('//analytics/analytics_header', array());?>

<main class="oe-analytics flex-layout flex-top ">
    <?php $this->renderPartial('//analytics/analytics_sidebar',
        array('specialty'=>$specialty)
    ); ?>
    <div class="analytics-charts">
        <?php if ($specialty === 'Cataract'){ ?>
        <div class="mdl-layout__container" style="width: 60%">
        <?php  $this->renderPartial('//analytics/analytics_cataract'); ?>
        </div>
        <?php } else {
            $this->renderPartial('//analytics/analytics_service',
                array('service_data'=>$service_data));

            $this->renderPartial('//analytics/analytics_clinical',
                array('clinical_data'=>$clinical_data));

            $this->renderPartial('//analytics/analytics_custom',
                array('custom_data'=> $custom_data));
        }?>
    </div>
        <?php $this->renderPartial('//analytics/analytics_drill_down_list', array(
            'patient_list' => $patient_list
        )); ?>
</main>

<script >
    $(document).ready(function() {
        OpenEyes.Dash.init('#pcr-risk-grid');
        OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk', null, 8);
        OpenEyes.Dash.init('#cataract-complication-grid');
        OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=CataractComplications', null,8);
        OpenEyes.Dash.init('#visual-acuity-grid');
        OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome', null, 8);
        OpenEyes.Dash.init('#refractive-outcome-grid');
        OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome', null, 8);


    });

</script>