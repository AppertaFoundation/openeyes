<?php
/* @var $data Patient
 * @var $this CaseSearchController
 * @var $trialPatient TrialPatient
 */

$navIconsUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';
$warnings = array();
foreach ($data->getWarnings(true) as $warn) {
    $warnings[] = "{$warn['long_msg']}: {$warn['details']}";
}
$inTrial = false;
$data->hasAllergyStatus();
?>
<tr>
    <td>

        <?php
        /** @var $patientPanel PatientPanel*/
        $patientPanel = $this->createWidget('application.widgets.PatientPanel', array(
            'patient' => $data,
            'layout' => 'list'
        ));
        $patientPanel->render('PatientPanel_list');
        ?>
    </td>
</tr>

<?php return;?>
<!--<tr class="cols-full" --><?php //echo ($inTrial) ? 'style="background-color: #fafad2;"' : '' ?><!-->-->
<!--    <td>-->
<!--        <h3 class="box-title">-->
<!--            --><?php //echo CHtml::link($data->contact->last_name
//                . ', ' . $data->contact->first_name
//                . ($data->is_deceased ? ' (Deceased)' : ''),
//                array('/patient/view', 'id' => $data->id),
//                array('target' => '_blank')
//            );
//            ?>
<!--            <!--        -->--><?php ////if (count($warnings) > 0): ?>
<!--            <!--          <span class="warning">-->-->
<!--            <!--          <span class="icon icon-alert icon-alert-warning" onmouseover="showWarning(this);"-->-->
<!--            <!--                onmouseleave="hideWarning(this);"></span>-->-->
<!--            <!--          <span class="quicklook warning">-->-->
<!--            <!--            <ul>-->-->
<!--            <!--              <li style="color: #fff;">-->-->
<!--            <!--                -->--><?php ////echo implode('</li><li style="color: #fff;">', $warnings) ?>
<!--            <!--              </li>-->-->
<!--            <!--            </ul>-->-->
<!--            <!--          </span>-->-->
<!--            <!--        </span>-->-->
<!--            <!--        -->--><?php ////endif; ?>
<!--        </h3>-->
<!--        <div class="">-->
<!--            <div class="cols-12 column">-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </td>-->
<!--    <td>-->
<!--        --><?php //if ($data->hasAllergyStatus()): ?>
<!--            <div class="patient-allergies-risks risk-warning"-->
<!--                 id="js-allergies-risks-btn"-->
<!--                 data-patient-id="--><?//= $data->id ?><!--"-->
<!--                 style=""-->
<!--            >-->
<!--                --><?//= $data->allergyAssignments ? 'Allergies' : ''; ?>
<!--                --><?//= $data->allergyAssignments && $data->risks ? ', ' : ''; ?>
<!--                --><?//= $data->risks || $data->getDiabetes() ? 'Alerts' : ''; ?>
<!--            </div>-->
<!--            <div class="oe-patient-popup"-->
<!--                 id="patient-popup-allergies-risks"-->
<!--                 style="-->
<!--                    display: none;-->
<!--                    width: 300px;-->
<!--                    top: unset;-->
<!--                    left: unset;-->
<!--                    padding: 0px;"-->
<!--                 data-patient-id="--><?//= $data->id ?><!--"-->
<!--            >-->
<!--                <div class="">-->
<!--                    --><?php
//                    /** @var $allergyWidget \OEModule\OphCiExamination\widgets\Allergies*/
//                    $allergyWidget = $this->createWidget(
//                        OEModule\OphCiExamination\widgets\Allergies::class,
//                        array(
//                            'patient' => $data,
//                            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
//                        )
//                    );
//                    $allergyWidget->render('Allergies_search_mode', $allergyWidget->getViewData());
//                    ?>
<!--                </div><!-- .popup-overflow -->-->
<!--            </div>-->
<!--        --><?php //endif; ?>
<!--    </td>-->
<!--    <td>-->
<!--        --><?php //echo "{$data->getGenderString()} ({$data->getAge()})"; ?>
<!--    </td>-->
<!---->
<!--    <!--    -->--><?php ////$this->widget('PatientDiagnosesAndMedicationsWidget',
//    //        array(
//    //            'patient' => $data,
//    //        )
//    //    ); ?>
<!--    <script>-->
<!--        function showWarning(item) {-->
<!--            $(item).siblings(".warning").show('fast');-->
<!--        }-->
<!---->
<!--        function hideWarning(item) {-->
<!--            $(item).siblings(".warning").hide('fast');-->
<!--        }-->
<!---->
<!--        function showQuicklook(item) {-->
<!--            $(item).siblings(".quicklook").show('fast');-->
<!--        }-->
<!---->
<!--        function hideQuicklook(item) {-->
<!--            $(item).siblings(".quicklook").hide('fast');-->
<!--        }-->
<!--        --><?php //if($data->hasAllergyStatus()){?>
//
//        var risks = new OpenEyes.UI.NavBtnPopup(
//            'risks_' + <?//= $data->id ?>//,
//            $('[id=js-allergies-risks-btn][data-patient-id=<?//= $data->id ?>//]'),
//            $('[id=patient-popup-allergies-risks][data-patient-id=<?//= $data->id ?>//]')
//        );
//        // risks.init();
//        risks.latchable = true;
//        risks.useMouseEvents = true;
//
//        <?php //}?>
<!--    </script>-->
<!--</tr>-->
