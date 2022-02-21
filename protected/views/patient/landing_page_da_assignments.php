<?php
    $criteria = new CDbCriteria();
    $criteria->with =['pathway', 'pathway.worklist_patient'];
    $criteria->compare('short_name', 'drug admin');
    $criteria->compare('worklist_patient.patient_id', $patient->id);
    $criteria->compare("DATE_FORMAT(worklist_patient.`when`, '%Y-%m-%d')", date('Y-m-d'));
    $steps = PathwayStep::model()->findAll($criteria);
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
                                <?php foreach ($steps as $step) {
                                        $assignment_id = $step->getState('assignment_id');
                                        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
                                    ?>
                                    <span 
                                        data-worklist-id=<?=$assignment->visit_id?> 
                                        data-patient-id=<?=$patient->id?> 
                                        data-pathstep-id=<?=$step->id?>
                                        data-assignment-id=<?=$step->id?>
                                        class="oe-pathstep-btn <?=$assignment->getStatusDetails(false, $step)['css']?> process js-no-interaction" 
                                        data-pathstep-name="<?=$assignment->getAssignmentTypeAndName()['name']?>"
                                    >
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