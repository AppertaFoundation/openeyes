
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
<?php $this->renderPartial('//analytics/analytics_header', array());?>
<script>
    window.csv_data_for_report = {};
</script>
<main class="oe-analytics flex-layout flex-top cols-full">
    <div class="cols-3">
        <?php
        if ($specialty === 'Cataract'){
            $this->renderPartial('//analytics/analytics_sidebar_cataract',
                array(
                    'specialty'=>$specialty,
                    'user_list'=>$user_list,
                    'current_user'=>$current_user
                )
            );
        }else{
            $this->renderPartial('//analytics/analytics_sidebar',
                array(
                    'specialty'=>$specialty,
                    'user_list'=>$user_list,
                    'current_user'=>$current_user,
                    'common_disorders'=>$common_disorders
                )
            );
        }
        ?>
    </div>

    <div class="analytics-charts cols-9">
        <?php
            if ($specialty !== 'Cataract'){
                $this->renderPartial('//analytics/analytics_service',
                    array(
                        'service_data'=>$service_data,
                        'common_disorders'=>$common_disorders,
                    ));
            }
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                if ($specialty === 'Cataract'){ ?>
                    <div class="mdl-layout__container">
                    <?php  $this->renderPartial('//analytics/analytics_cataract'); ?>
                    </div>
                <?php } else { ?>
                    <div id="js-hs-chart-analytics-clinical-main" style="display: none;">
                     <?php
                     $this->renderPartial('//analytics/analytics_clinical',
                         array('clinical_data'=>$clinical_data)
                     );
                     if ($specialty !== "All"){
                            $this->renderPartial('//analytics/analytics_custom',
                                array(
                                    'custom_data'=>$custom_data,
                                    'specialty' => $specialty
                                )
                            );
                        }
                     ?>
                    </div>
                <?php
                }
            }
        ?>
        <div id="js-analytics-spinner" style="display: none;"><i class="spinner"></i></div>
    </div>
        <?php
        if ($specialty !== 'Cataract'){
            $this->renderPartial('//analytics/analytics_drill_down_list', array(
                'patient_list' => $patient_list
            ));
        } else {
            $this->renderPartial('//analytics/analytics_drill_down_list', array(
                'event_list'=> $event_list,
                'patient_list' => $patient_list
            ));
        }?>
</main>
<script>
    const plotly_min_width = 800;
    const plotly_min_height = 650;
    var page_width = $('.analytics-charts').width();
    var page_height = $('.oe-analytics').height()-50;
    var layout_width = plotly_min_width > page_width? plotly_min_width : page_width;
    var layout_height = plotly_min_height > page_height? plotly_min_height : page_height;

    analytics_layout['width'] = layout_width;
    analytics_layout['height'] = layout_height;

    <?php if($specialty === 'Cataract'){?>
        $( document ).ready(function () {
            OpenEyes.Dash.init('#pcr-risk-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk&template=analytics', null, 10);
        });
    <?php }?>
</script>