<?php

class m210906_070021_create_ophtrconsent_sup_consent_tables extends OEMigration
{
    public function safeUp()
    {
        // ---- Admin tables ----

        // Base question types
        $this->createOETable(
            'ophtrconsent_sup_consent_question_type',
            [
                'id' => 'pk',
                'name' => 'text null',
                'text_based' => 'boolean not null',
            ],
            false // there will be no front end editing of question types so no need for versioning.
        );

        $this->insertMultiple('ophtrconsent_sup_consent_question_type',
            [
                ['name' => 'radio', 'text_based' => 0 ],
                ['name' => 'check', 'text_based' => 0],
                ['name' => 'dropdown', 'text_based' => 0],
                ['name' => 'text', 'text_based' => 1],
                ['name' => 'textarea', 'text_based' => 1],
            ]
        );
        // Base question table
        $this->createOETable(
            'ophtrconsent_sup_consent_question',
            [
                'id' => 'pk',
                'question_type_id' => 'int(11) not null',
                'name' => 'text not null',
                'description' => 'text null',
            ],
            true
        );
        $this->addForeignKey('ophtrconsent_sup_consent_question_type_fk', 'ophtrconsent_sup_consent_question', 'question_type_id', 'ophtrconsent_sup_consent_question_type', 'id');

        // Base question assignment
        $this->createOETable(
            'ophtrconsent_sup_consent_question_assignment',
            [
                'id' => 'pk',
                'question_id' => 'int(11) not null',
                'question_text' => 'text not null',
                'question_info' => 'text null',
                'question_output' => 'text null',
                'default_option_text' => 'text null',
                'default_option_selection' => 'int(11) null',
                'minimum_selected' => 'int null',
                'maximum_selected' => 'int null',
                'required' => 'boolean not null default 1',
                'active' => 'boolean not null default 1',
                'display_order' => 'int not null default 0',
                'priority' => 'int not null default 0',
                //the above can be saved per bellow levels
                'institution_id' => 'int(10) unsigned null',
                'site_id' => 'int(10) unsigned null',
                'subspecialty_id' => 'int(10) unsigned null',
                'form_id' =>'int(10) unsigned null',
            ],
            true
        );
        $this->addForeignKey('ophtrconsent_sup_consent_question_as_question_fk', 'ophtrconsent_sup_consent_question_assignment', 'question_id', 'ophtrconsent_sup_consent_question', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_question_as_institution_fk', 'ophtrconsent_sup_consent_question_assignment', 'institution_id', 'institution', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_question_as_site_fk', 'ophtrconsent_sup_consent_question_assignment', 'site_id', 'site', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_question_as_subspecialty_fk', 'ophtrconsent_sup_consent_question_assignment', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_question_as_form_fk', 'ophtrconsent_sup_consent_question_assignment', 'form_id', 'ophtrconsent_type_type', 'id');

         // Base question Answers
        $this->createOETable(
            'ophtrconsent_sup_consent_question_answer',
            [
                'id' => 'pk',
                'question_assignment_id' => 'int(11) not null',
                'display_order' => 'int not null default 0',
                'name'=> 'text not null',
                'display'=>'text',
                'answer_output'=>'text',
            ],
            true
        );
        $this->addForeignKey('ophtrconsent_sup_consent_q_a_question_fk', 'ophtrconsent_sup_consent_question_answer', 'question_assignment_id', 'ophtrconsent_sup_consent_question_assignment', 'id');


        // ---- element tables ----

        // Element
        $this->createOETable(
            'et_ophtrconsent_sup_consent_element',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
            ],
            true
        );

        $this->addForeignKey('ophtrconsent_sup_consent_element_event_fk', 'et_ophtrconsent_sup_consent_element', 'event_id', 'event', 'id');

        // Element question
        $this->createOETable(
            'ophtrconsent_sup_consent_element_question',
            [
                'id' => 'pk',
                'element_id' => 'int(11) not null',
                'question_id' => 'int(11) not null',
            ],
            true
        );

        $this->addForeignKey('ophtrconsent_sup_consent_element_q_element_fk', 'ophtrconsent_sup_consent_element_question', 'element_id', 'et_ophtrconsent_sup_consent_element', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_element_q_question_fk', 'ophtrconsent_sup_consent_element_question', 'question_id', 'ophtrconsent_sup_consent_question_assignment', 'id');

        // Element question Answers
        $this->createOETable(
            'ophtrconsent_sup_consent_element_question_answer',
            [
                'id' => 'pk',
                'element_question_id' => 'int(11) not null',
                'answer_text' => 'text null',
                'answer_id' => 'int(11) null',
            ],
            true
        );

        $this->addForeignKey('ophtrconsent_sup_consent_element_qa_question_fk', 'ophtrconsent_sup_consent_element_question_answer', 'element_question_id', 'ophtrconsent_sup_consent_element_question', 'id');
        $this->addForeignKey('ophtrconsent_sup_consent_element_qa_answer_fk', 'ophtrconsent_sup_consent_element_question_answer', 'answer_id', 'ophtrconsent_sup_consent_question_answer', 'id');

        // Add element group and type
        $this->createElementGroupForEventType(
            'Supplementary consent',
            'OphTrConsent',
            130
        );
        $this->createElementType(
            'OphTrConsent',
            'Supplementary consent',
            [
                'class_name' => 'Element_OphTrConsent_SupplementaryConsent',
                'display_order' => 130,
                'group_name' => 'Supplementary consent',
                'default' => 1,
                'required' => 1,
            ]
        );
    }

    public function safeDown()
    {
        // delete element type and group
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_SupplementaryConsent'
        );

        $this->deleteElementGroupForEventType(
            'Supplementary consent',
            'OphTrConsent'
        );

        // Element question Answers
        $this->dropOETable('ophtrconsent_sup_consent_element_question_answer', true);

        // Element question
        $this->dropOETable('ophtrconsent_sup_consent_element_question', true);

        // Element
        $this->dropOETable('et_ophtrconsent_sup_consent_element', true);

        // Base question Answers
        $this->dropOETable('ophtrconsent_sup_consent_question_answer', true);

        // Base question assignment
        $this->dropOETable('ophtrconsent_sup_consent_question_assignment', true);

        // Base question table
        $this->dropOETable('ophtrconsent_sup_consent_question', true);

        // Base question types
        $this->dropOETable('ophtrconsent_sup_consent_question_type', false);
    }
}
