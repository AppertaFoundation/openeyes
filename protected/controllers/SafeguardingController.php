<?php

class SafeguardingController extends BaseController
{
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index'),
                'roles' => array('Safeguarding'),
            )
        );
    }

    public function actionIndex() {
        $criteria = new CDbCriteria();
        $params = array();

        if(isset($_GET['safeguarding_filters'])) {
            $filters = $_GET['safeguarding_filters'];

            //Patient criteria
            $age_range_from = $filters['age_from'] ?? null;

            if(!empty($age_range_from)) {
                $from_date = new DateTime();
                $from_date->sub(new DateInterval("P".$age_range_from."Y"));

                $criteria->addCondition('patient.dob <= :dob_from');
                $params[':dob_from'] = $from_date->format('Y-m-d H:i:s');
            }

            $age_range_to = $filters['age_to'] ?? null;

            if(!empty($age_range_to)) {
                $age_range_to += 1;//To make the year inclusive

                $to_date = new DateTime();
                $to_date->sub(new DateInterval("P".$age_range_to."Y"));

                $criteria->addCondition('patient.dob >= :dob_to');
                $params[':dob_to'] = $to_date->format('Y-m-d H:i:s');
            }

            //Safeguarding element critera
            $has_social_worker = $filters['has_social_worker'] ?? null;
            if(isset($has_social_worker) && $has_social_worker == 1) {
                $criteria->addCondition('has_social_worker=1');
            }

            $under_protection_plan = $filters['under_protection_plan'] ?? null;
            if(isset($under_protection_plan) && $under_protection_plan == 1) {
                $criteria->addCondition('under_protection_plan=1');
            }

            //Safeguarding entry criteria
            if(!empty($filters['safeguarding_concern_id'])) {
                $criteria->addCondition('entries.concern_id = :safeguarding_concern_id');
                $params[':safeguarding_concern_id'] = $filters['safeguarding_concern_id'];
            }
        }

        $criteria->addCondition('outcome_id IS NULL OR outcome_id=:followup_outcome_id');
        $criteria->addCondition('no_concerns = 0');
        $params[':followup_outcome_id'] = \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::FOLLOWUP_REQUIRED;

        $criteria->params = $params;
        $safeguarding_elements = \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::model()
            ->with(array('entries' => array(), 'event' => array('with' => array('episode' => array('with' => 'patient')))))
            ->findAll($criteria);

        $this->render('/safeguarding/index', array('elements' => $safeguarding_elements));
    }
}
