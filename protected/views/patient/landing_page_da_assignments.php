<?php
    $criteria = new CDbCriteria();
    $criteria->compare('t.patient_id', $patient->id);
    $criteria->addCondition('t.visit_id IS NOT NULL');
    $criteria->compare('t.active', 1);
    $criteria->compare("DATE_FORMAT(worklist_patient.`when`, '%Y-%m-%d')", date('Y-m-d'));
    $assignments = \OphDrPGDPSD_Assignment::model()->with('pgdpsd')->with('worklist_patient')->findAll($criteria);
    Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
?>
<section class="element full">
    <header class="element-header">
        <h3 class="element-title">Worklist activity</h3>
    </header>
    <div class="element-data full-width">
        <div class="row">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-6">
                </colgroup>
                <tbody>
                    <tr>
                        <th>Today's drug administration:</th>
                        <!-- loop here -->
                        <td>
                            <div>
                                <?php foreach ($assignments as $assignment) {?>
                                    <span data-worklist-id=<?=$assignment->visit_id?> data-patient-id=<?=$patient->id?> data-pathstep-id=<?=$assignment->id?> class="oe-pathstep-btn <?=$assignment->getStatusDetails()['css']?> process" data-pathstep-name="<?=$assignment->getAssignmentTypeAndName()['name']?>">
                                        <span class="step i-drug-admin"></span>
                                    </span>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
    $(function(){
        new OpenEyes.UI.PathStep({interactive: 0});
    })
</script>