
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php $this->renderPartial('//analytics/analytics_header', array());?>
<script>
    window.csv_data_for_report = {};
</script>
<main class="oe-analytics flex-layout flex-top " style="width: 100%;">
    <?php
        if ($specialty === 'Cataract'){
            $this->renderPartial('//analytics/analytics_sidebar_cataract',
                array('specialty'=>$specialty,'user_list'=>$user_list,'current_user'=>$current_user)
            );
        }else{
            $this->renderPartial('//analytics/analytics_sidebar',
                array('specialty'=>$specialty,'user_list'=>$user_list,'current_user'=>$current_user, 'common_disorders'=>$common_disorders)
            );
        }
 ?>
    <div class="analytics-charts">
        <?php
            if ($specialty !== 'Cataract'){
                $this->renderPartial('//analytics/analytics_service',
                    array('service_data'=>$service_data,'common_disorders'=>$common_disorders));
            }
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                if ($specialty === 'Cataract'){?>
                    <div class="mdl-layout__container">
                    <?php  $this->renderPartial('//analytics/analytics_cataract',
                    array('event_list'=> $event_list)); ?>
                    </div>
                <?php }else{?>
                    <div id="js-hs-chart-analytics-clinical-main">
                     <?php
                     $this->renderPartial('//analytics/analytics_clinical',
                         array('clinical_data'=>$clinical_data)
                     );
                     if ($specialty !== "All"){
                            $this->renderPartial('//analytics/analytics_custom', array('custom_data'=>$custom_data));
                        }
                     ?>
                    </div>
                <?php
                }
            }
        ?>
    </div>
        <?php
        if ($specialty !== 'Cataract'){
            $this->renderPartial('//analytics/analytics_drill_down_list', array(
                'patient_list' => $patient_list
            ));
        }
        ?>
</main>
<script>
    <?php if($specialty === 'Cataract'){?>
        $( document ).ready(function () {
            OpenEyes.Dash.init('#pcr-risk-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk&template=analytics', null, 10);
            $('.mdl-cell').css('height','600px');
            $('.mdl-cell').css('width','1000px');
        });
    <?php }?>
</script>