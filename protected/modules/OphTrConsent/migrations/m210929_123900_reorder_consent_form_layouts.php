<?php

class m210929_123900_reorder_consent_form_layouts extends OEMigration
{
    private const TYPE_PATIENT_AGREEMENT_ID = 1;
    private const TYPE_PARENTAL_AGREEMENT_ID = 2;
    private const TYPE_PATIENT_PARENTAL_AGREEMENT_ID = 3;
    private const TYPE_UNABLE_TO_CONSENT_ID = 4;

    private array $consent_elements = [];
    private array $consent_layout_1 = [
        'Element_OphTrConsent_Type',
        'Element_OphTrConsent_Esign',
        'Element_OphTrConsent_Procedure',
        'Element_OphTrConsent_ExtraProcedures',
        'Element_OphTrConsent_Leaflets',
        'Element_OphTrConsent_BenefitsAndRisks',
        'Element_OphTrConsent_SupplementaryConsent',
        'Element_OphTrConsent_Specialrequirements',
        'Element_OphTrConsent_PatientQuestions',
        'Element_OphTrConsent_AdvancedDecision',
        'Element_OphTrConsent_Consenttakenby',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures',
        'Element_OphTrConsent_Copies'
    ];

    private array $consent_layout_2 = [
        'Element_OphTrConsent_Type',
        'Element_OphTrConsent_Esign',
        'Element_OphTrConsent_Procedure',
        'Element_OphTrConsent_ExtraProcedures',
        'Element_OphTrConsent_Leaflets',
        'Element_OphTrConsent_BenefitsAndRisks',
        'Element_OphTrConsent_SupplementaryConsent',
        'Element_OphTrConsent_Specialrequirements',
        'Element_OphTrConsent_PatientQuestions',
        'Element_OphTrConsent_AdvancedDecision',
        'Element_OphTrConsent_Consenttakenby',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures',
        'Element_OphTrConsent_Copies'
    ];

    private array $consent_layout_3 = [
        'Element_OphTrConsent_Type',
        'Element_OphTrConsent_Esign',
        'Element_OphTrConsent_Procedure',
        'Element_OphTrConsent_ExtraProcedures',
        'Element_OphTrConsent_Leaflets',
        'Element_OphTrConsent_BenefitsAndRisks',
        'Element_OphTrConsent_SupplementaryConsent',
        'Element_OphTrConsent_Specialrequirements',
        'Element_OphTrConsent_PatientQuestions',
        'Element_OphTrConsent_AdvancedDecision',
        'Element_OphTrConsent_Consenttakenby',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures',
        'Element_OphTrConsent_Copies'
    ];
    private array $consent_layout_4 = [
        'Element_OphTrConsent_Type',
        'Element_OphTrConsent_Esign',
        'Element_OphTrConsent_Procedure',
        'Element_OphTrConsent_Specialrequirements',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_CapacityAssessment',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_PatientAttorneyDeputy',
        'OEModule\OphTrConsent\models\Element_OphTrConsent_MedicalCapacityAdvocate',
        'Element_OphTrConsent_OthersInvolvedDecisionMakingProcess',
        'Element_OphTrConsent_Consenttakenby',
        'Element_OphTrConsent_Copies'
    ];

    public function safeUp()
    {
        $this->truncateTable('ophtrconsent_type_assessment');

        $this->consent_elements = $this->dbConnection->createCommand('
            SELECT id, class_name FROM element_type WHERE event_type_id =
              (
                SELECT id FROM event_type WHERE NAME = "Consent form"
              )')
            ->queryAll();

        $layout_elements_1 = $this->getLayoutElements($this->consent_layout_1);
        $this->insertLayoutElements($layout_elements_1, self::TYPE_PATIENT_AGREEMENT_ID);

        $layout_elements_2 = $this->getLayoutElements($this->consent_layout_2);
        $this->insertLayoutElements($layout_elements_2, self::TYPE_PARENTAL_AGREEMENT_ID);

        $layout_elements_3 = $this->getLayoutElements($this->consent_layout_3);
        $this->insertLayoutElements($layout_elements_3, self::TYPE_PATIENT_PARENTAL_AGREEMENT_ID);

        $layout_elements_4 = $this->getLayoutElements($this->consent_layout_4);
        $this->insertLayoutElements($layout_elements_4, self::TYPE_UNABLE_TO_CONSENT_ID);
    }

    public function safeDown()
    {
        $this->delete('ophtrconsent_type_assessment', 'type_id = ' . self::TYPE_PATIENT_AGREEMENT_ID);
        $this->delete('ophtrconsent_type_assessment', 'type_id = ' . self::TYPE_PARENTAL_AGREEMENT_ID);
        $this->delete('ophtrconsent_type_assessment', 'type_id = ' . self::TYPE_PATIENT_PARENTAL_AGREEMENT_ID);
        $this->delete('ophtrconsent_type_assessment', 'type_id = ' . self::TYPE_UNABLE_TO_CONSENT_ID);
    }

    /**
     * Get available and required Consent elements and set order
     * @param $required_elements
     * @return array
     */
    private function getLayoutElements($required_elements): array
    {
        $result = [];
        foreach ($required_elements as $r_key => $required) {
            if (array_search($required, array_column($this->consent_elements, 'class_name')) !== false) {
                $key = array_search($required, array_column($this->consent_elements, 'class_name'));
                $result[$r_key] = $this->consent_elements[$key];
            }
        }
        return $result;
    }

    /**
     * Insert layout elements
     * @param $layout_elements
     */
    private function insertLayoutElements($layout_elements, $type_id): void
    {
        if (!empty($layout_elements)) {
            foreach ($layout_elements as $key => $element) {
                echo("\n" . $element['id'] . ", " . $type_id . ", " . $key);
                $this->execute("
                    INSERT INTO ophtrconsent_type_assessment (element_id, type_id, display_order )
                    VALUES
                    (
                        " . $element['id'] . ", " . $type_id . ", " . $key . "
                    )
                ");
            }
        }
    }
}
