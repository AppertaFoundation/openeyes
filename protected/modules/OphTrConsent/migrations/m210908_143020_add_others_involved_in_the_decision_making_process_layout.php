<?php

class m210908_143020_add_others_involved_in_the_decision_making_process_layout extends OEMigration
{
    public function safeUp()
    {
        $element_id = $this->getIdOfElementTypeByClassName('Element_OphTrConsent_OthersInvolvedDecisionMakingProcess');
        $this->execute("
            INSERT INTO ophtrconsent_type_assessment (element_id, type_id, display_order )
            VALUES
            (
                " . $element_id . ",
                " . Element_OphTrConsent_OthersInvolvedDecisionMakingProcess::TYPE_PATIENT_AGREEMENT_ID . ",
                10
            )
        ");
    }

    public function safeDown()
    {
        $element_id = $this->getIdOfElementTypeByClassName('Element_OphTrConsent_OthersInvolvedDecisionMakingProcess');
        $this->delete('ophtrconsent_type_assessment', 'element_id = :element_id', [':element_id' => $element_id]);
    }
}
