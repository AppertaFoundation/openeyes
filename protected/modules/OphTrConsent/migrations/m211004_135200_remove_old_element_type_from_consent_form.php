<?php

class m211004_135200_remove_old_element_type_from_consent_form extends OEMigration
{
    private array $consent_elements = array(
        'OEModule\OphTrConsent\models\Element_OphTrConsent_ConsultantSignature' => array('name' => 'Health professional signature', 'display_order' => 130, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_InterpreterSignature' => array('name' => 'Interpreter\'s signature', 'display_order' => 140, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_PatientSignature' => array('name' => 'Patient signature', 'display_order' => 150, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_WitnessSignature' => array('name' => 'Witness signature', 'display_order' => 160, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_ParentSignature' => array('name' => 'Parent\'s signature', 'display_order' => 170, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_ChildSignature' => array('name' => 'Child\'s signature', 'display_order' => 180, 'default' => 1, 'required' => 1),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_Confirmation_ConsultantSignature' => array('name' => 'Health professional signature', 'display_order' => 210, 'default' => 0, 'required' => 0),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_Confirmation' => array('name' => 'Confirmation of consent', 'display_order' => 190, 'default' => 0, 'required' => 0),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_Withdrawal_PatientSignature' => array('name' => 'Patient signature', 'display_order' => 240, 'default' => 0, 'required' => 0),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_Withdrawal' => array('name' => 'Withdrawal of consent', 'display_order' => 220, 'default' => 0, 'required' => 0),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision_DeputySignature' => array('name' => 'Power of attorney\'s or deputy\'s signature', 'display_order' => 250, 'default' => 1, 'required' => 0),
        'OEModule\OphTrConsent\models\Element_OphTrConsent_ConsultantSignatureWithSecondOpinion' => array('name' => 'Signature of Health Professional proposing treatment', 'display_order' => 260, 'default' => 1, 'required' => 1)
    );

    public function safeUp()
    {
        foreach ($this->consent_elements as $class_name => $element) {
            $this->deleteElementType('OphTrConsent', $class_name);
        }
    }

    public function safeDown()
    {
        foreach ($this->consent_elements as $class_name => $element) {
            $this->createElementType(
                'OphTrConsent',
                $element['name'],
                array(
                    'class_name'    => $class_name,
                    'display_order' => $element['display_order'],
                    'default'       => $element['default'],
                    'required'  => $element['required'],
                )
            );
        }
    }
}
