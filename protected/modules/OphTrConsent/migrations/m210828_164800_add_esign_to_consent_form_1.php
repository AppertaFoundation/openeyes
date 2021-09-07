<?php

class m210828_164800_add_esign_to_consent_form_1 extends OEMigration
{
    private const TYPE_PATIENT_AGREEMENT_ID = 1;
    private const TYPE_PARENTAL_AGREEMENT_ID = 2;
    private const TYPE_PATIENT_PARENTAL_AGREEMENT_ID = 3;
    private const TYPE_UNABLE_TO_CONSENT_ID = 4;

    private array $consent_elements = [];

    public function safeUp()
    {
        $this->consent_elements = $this->dbConnection->createCommand('
            SELECT id, class_name FROM element_type WHERE event_type_id =
              (
                SELECT id FROM event_type WHERE NAME = "Consent form"
              )')
            ->queryAll();
        $layout_elements = $this->getLayoutElements([7 => 'Element_OphTrConsent_Esign']);
        $this->insertLayoutElements($layout_elements, self::TYPE_PATIENT_AGREEMENT_ID);
    }

    public function safeDown()
    {
        $element_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name', [':class_name' => 'Element_OphTrConsent_Esign'])
            ->queryScalar();
        $this->delete('ophtrconsent_type_assessment', 'type_id = ' . self::TYPE_PATIENT_AGREEMENT_ID . ' and element_id = '.$element_id);
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
