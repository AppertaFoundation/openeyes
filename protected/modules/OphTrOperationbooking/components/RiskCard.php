<?php

use OEModule\OphCiExamination\models\OphCiExaminationRisk;

/**
 * Class RiskCard
 * @property $data OphTrOperationbooking_Whiteboard
 */
class RiskCard extends WBCard
{
    protected $baseViewFile = 'wb_allergies_and_risks';
    protected $type = 'Special';
    protected $alpha_risk;
    protected $anticoag_risk;

    public function init()
    {
        // We are deliberately overriding this here because we don't want the generic initialisation to occur.
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('name', 'Alpha blockers');
        $this->alpha_risk = OphCiExaminationRisk::model()->find($criteria);

        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('name', 'Anticoagulants');
        $this->anticoag_risk = OphCiExaminationRisk::model()->find($criteria);
    }

    /**
     * @return OphCiExaminationRisk
     */
    public function getAlphaBlockerRisk()
    {
        return $this->alpha_risk;
    }

    /**
     * @return OphCiExaminationRisk
     */
    public function getAnticoagulantRisk()
    {
        return $this->anticoag_risk;
    }
}
