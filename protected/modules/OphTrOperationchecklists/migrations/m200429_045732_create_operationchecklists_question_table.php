<?php

class m200429_045732_create_operationchecklists_question_table extends OEMigration
{
    private $questions_array = array(
        'Admission' => array(
            array(
                'id' => 1,
                'question' => 'Have there been any changes in the patient\'s health since Pre-operative Assessment?',
                'type' => 'RADIO',
                'mandatory' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 10
            ),
            array(
                'id' => 2,
                'question' => 'Are there any changes to the patient\'s medication?',
                'type' => 'RADIO',
                'mandatory' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 20
            ),
            array(
                'id' => 3,
                'question' => 'Has the patient followed pre-operative fasting instructions?',
                'type' => 'RADIO',
                'mandatory' => 0,
                'is_comment_field_required' => 1,
                'display_order' => 30
            ),
            array(
                'id' => 4,
                'question' => 'Last ate at',
                'type' => 'RADIO_TIME',
                'mandatory' => 0,
                'is_comment_field_required' => 0,
                'display_order' => 40
            ),
            array(
                'id' => 5,
                'question' => 'Last drank at',
                'type' => 'RADIO_TIME',
                'mandatory' => 0,
                'is_comment_field_required' => 0,
                'display_order' => 50
            ),
            array(
                'id' => 6,
                'question' => 'Arrangements made for responsible adult to accompany patient home and look after patient for 24 hours?',
                'type' => 'RADIO',
                'mandatory' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 60
            ),
            array(
                'id' => 7,
                'question' => 'Name of contact',
                'type' => 'TEXT',
                'mandatory' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 70
            ),
            array(
                'id' => 8,
                'question' => 'Contact telephone number',
                'type' => 'TEXT',
                'mandatory' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 70
            ),
            array(
                'id' => 9,
                'question' => 'Mode of transport',
                'type' => 'TEXT',
                'mandatory' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 80
            ),
            array(
                'id' => 10,
                'question' => 'Sick note required?',
                'type' => 'RADIO',
                'mandatory' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 90
            ),
            array(
                'id' => 12,
                'question' => 'Pre-operative drops',
                'type' => 'SECTION',
                'mandatory' => 0,
                'is_comment_field_required' => 0,
                'display_order' => 120
            ),
            array(
                'id' => 13,
                'question' => 'COVID-19 swab result',
                'type' => 'RADIO',
                'mandatory' => 0,
                'is_comment_field_required' => 0,
                'display_order' => 130
            ),
            array(
                'id' => 14,
                'question' => 'Observations',
                'type' => 'SECTION',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 130
            )
        ),
        'Documentation' => array(
            array(
                'id' => 15,
                'question' => 'Identity Bracelet',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 140
            ),
            array(
                'id' => 16,
                'question' => 'Consent form',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 150
            ),
            array(
                'id' => 17,
                'question' => 'Verification of consent',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 160
            ),
            array(
                'id' => 18,
                'question' => 'Correct site surgery form signed',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 170
            ),
            array(
                'id' => 19,
                'question' => 'Operation site marked',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 180
            ),
            array(
                'id' => 20,
                'question' => 'Other',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 190
            ),
            array(
                'id' => 21,
                'question' => 'Fasted',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 0,
                'requires_answer' => 0,
                'display_order' => 200
            ),
            array(
                'id' => 22,
                'question' => 'Food',
                'type' => 'RADIO_TIME',
                'mandatory' => null,
                'is_comment_field_required' => 0,
                'display_order' => 210
            ),
            array(
                'id' => 23,
                'question' => 'Water',
                'type' => 'RADIO_TIME',
                'mandatory' => null,
                'is_comment_field_required' => 0,
                'display_order' => 220
            ),
            array(
                'id' => 24,
                'question' => 'Allergies checked',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 230
            ),
        ),
        'Clinical Assessment' => array(
            array(
                'id' => 25,
                'question' => 'Has the patient been seen by Anaesthetist prior to escort to Theatre',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 140
            ),
            array(
                'id' => 26,
                'question' => 'Has the patient been seen by Surgeon prior to escort to Theatre',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 150
            ),
        ),
        'Nursing / Practitioner Assessment' => array(
            array(
                'id' => 27,
                'question' => 'Oxygen therapy in situ',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 160
            ),
            array(
                'id' => 28,
                'question' => 'Amount (L/min)',
                'type' => 'TEXT',
                'is_hidden' => 1,
                'mandatory' => null,
                'is_comment_field_required' => 0,
                'display_order' => 170
            ),
            array(
                'id' => 29,
                'question' => 'Mode of Administration',
                'type' => 'DROPDOWN',
                'is_hidden' => 1,
                'mandatory' => null,
                'is_comment_field_required' => 0,
                'display_order' => 180
            ),
            array(
                'id' => 30,
                'question' => 'HCAI status',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 190
            ),
            array(
                'id' => 31,
                'question' => 'Weight',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 200
            ),
            array(
                'id' => 32,
                'question' => 'Diabetic protocol in place',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 210
            ),
            array(
                'id' => 33,
                'question' => 'Blood Sugar',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 220
            ),
            array(
                'id' => 34,
                'question' => 'Jewellery/Body piercing',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 230
            ),
            array(
                'id' => 35,
                'question' => 'False nails/Nail varnish removed',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 240
            ),
            array(
                'id' => 36,
                'question' => 'Dentures removed',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 250
            ),
            array(
                'id' => 37,
                'question' => 'Caps/Crowns/Loose teeth',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 260
            ),
            array(
                'id' => 38,
                'question' => 'Hearing aid/Contact lenses/Glasses',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 270
            ),
            array(
                'id' => 39,
                'question' => 'Implants/Prosthesis/Pacemaker',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 280
            ),
        ),
        'Patient Support' => array(
            array(
                'id' => 42,
                'question' => 'Interpreter present',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 310
            ),
            array(
                'id' => 43,
                'question' => 'Accompanied to theatre',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_comment_field_required' => 1,
                'display_order' => 320
            ),
        ),
        'Discharge' => array(
            array(
                'id' => 44,
                'question' => 'Is the patient being transferred to another Ward?',
                'type' => 'RADIO',
                'mandatory' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 160
            ),
            array(
                'id' => 45,
                'question' => 'Return from Theatre Date',
                'type' => 'DATE',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 170
            ),
            array(
                'id' => 46,
                'question' => 'Return from Theatre Time',
                'type' => 'TIME',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 170
            ),
            array(
                'id' => 47,
                'question' => 'Blood pressure within acceptable level?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 180
            ),
            array(
                'id' => 48,
                'question' => 'Venflon / ECG electrodes removed?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 190
            ),
            array(
                'id' => 49,
                'question' => 'To be accompanied home?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 200
            ),
            array(
                'id' => 50,
                'question' => 'Patient Carer Understands:',
                'mandatory' => null,
                'is_hidden' => 1,
                'requires_answer' => 0,
                'is_comment_field_required' => 1,
                'display_order' => 210
            ),
            array(
                'id' => 51,
                'question' => 'How to instil eye drops',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 210
            ),
            array(
                'id' => 52,
                'question' => 'Frequency of eye drops',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 220
            ),
            array(
                'id' => 53,
                'question' => 'How to obtain further supplies',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 230
            ),
            array(
                'id' => 54,
                'question' => 'Specific instructions',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 240
            ),
            array(
                'id' => 55,
                'question' => 'Actions to take if complications suspected',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 250
            ),
            array(
                'id' => 56,
                'question' => 'District nurse arranged?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 260
            ),
            array(
                'id' => 57,
                'question' => 'Post-op instruction booklet given?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 270
            ),
            array(
                'id' => 58,
                'question' => 'Ambulance / hospital car booked?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 280
            ),
            array(
                'id' => 59,
                'question' => 'Contact number given?',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 290
            ),
            array(
                'id' => 60,
                'question' => 'Follow up',
                'type' => 'RADIO',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 1,
                'display_order' => 300
            ),
            array(
                'id' => 62,
                'question' => 'Comments',
                'type' => 'TEXT',
                'mandatory' => null,
                'is_hidden' => 1,
                'is_comment_field_required' => 0,
                'display_order' => 320
            ),
        )
    );

    private $questionRelationships = array(
        array(
            'parent_question_id' => 3,
            'sub_question_id' => 4,
        ),
        array(
            'parent_question_id' => 3,
            'sub_question_id' => 5,
        ),
        array(
            'parent_question_id' => 6,
            'sub_question_id' => 7,
        ),
        array(
            'parent_question_id' => 6,
            'sub_question_id' => 8,
        ),
        array(
            'parent_question_id' => 6,
            'sub_question_id' => 9,
        ),
        array(
            'parent_question_id' => 21,
            'sub_question_id' => 22,
        ),
        array(
            'parent_question_id' => 21,
            'sub_question_id' => 23,
        ),
        array(
            'parent_question_id' => 27,
            'sub_question_id' => 28,
        ),
        array(
            'parent_question_id' => 27,
            'sub_question_id' => 29,
        ),
        array(
            'parent_question_id' => 50,
            'sub_question_id' => 51,
        ),
        array(
            'parent_question_id' => 50,
            'sub_question_id' => 52,
        ),
        array(
            'parent_question_id' => 50,
            'sub_question_id' => 53,
        ),
        array(
            'parent_question_id' => 50,
            'sub_question_id' => 54,
        ),
        array(
            'parent_question_id' => 50,
            'sub_question_id' => 55,
        )
    );

    private $questionSections = array(
        array(
            'question_id' => 12,
            'section_name' => 'OphTrOperationchecklists_Dilation',
        ),
        array(
            'question_id' => 14,
            'section_name' => 'OphTrOperationchecklists_Observations',
        ),
    );

    // Use safeUp/safeDown to do migration with transaction
    public function up()
    {
        // Creating Table
        $this->createOETable('ophtroperationchecklists_questions', array(
            'id' => 'pk',
            'element_type_id' => 'int(10) unsigned',
            'question' => 'text NOT NULL',
            'type' => 'enum("TEXT", "RADIO", "DROPDOWN", "DATE", "SECTION", "TIME", "RADIO_TIME") DEFAULT NULL COMMENT "type of response"',
            'mandatory' => 'boolean NULL',
            'is_hidden' => 'boolean DEFAULT false',
            'requires_answer' => 'boolean DEFAULT true',
            'is_comment_field_required' => 'boolean NOT NULL',
            'display_order' => 'int NOT NULL'
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_cq_etid_fk',
            'ophtroperationchecklists_questions',
            'element_type_id',
            'element_type',
            'id'
        );

        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name="OphTrOperationchecklists"')
            ->queryScalar();

        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            // Inserting values in a transaction so that the id's of the questions are available when inserting
            // the values in the "ophtroperationchecklists_question_relationships" table the question_id.
            foreach ($this->questions_array as $element_name => $questions) {
                $element_id = $this->dbConnection->createCommand()
                    ->select('id')
                    ->from('element_type')
                    ->where('name=:name', array(':name' => $element_name))
                    ->andWhere('event_type_id=' . $event_type_id)
                    ->queryRow();
                foreach ($questions as $question) {
                    $question['element_type_id'] = $element_id['id'];
                    $this->insert('ophtroperationchecklists_questions', $question);
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
        }

        $this->createQuestionRelationshipsTable();
        $this->createSectionTable();
    }

    public function down()
    {
        $this->dropForeignKey(
            'ophtroperationchecklists_cqr_sqid_fk',
            'ophtroperationchecklists_question_relationships'
        );
        $this->dropForeignKey(
            'ophtroperationchecklists_cqr_pqid_fk',
            'ophtroperationchecklists_question_relationships'
        );
        $this->dropOETable('ophtroperationchecklists_question_section', true);
        $this->dropOETable('ophtroperationchecklists_question_relationships', true);
        $this->dropOETable('ophtroperationchecklists_questions', true);
    }

    private function createQuestionRelationshipsTable()
    {
        // Creating Table
        $this->createOETable('ophtroperationchecklists_question_relationships', array(
            'id' => 'pk',
            'parent_question_id' => 'int(11)',
            'sub_question_id' => 'int(11)',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_cqr_pqid_fk',
            'ophtroperationchecklists_question_relationships',
            'parent_question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_cqr_sqid_fk',
            'ophtroperationchecklists_question_relationships',
            'sub_question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->populateQuestionRelationsTable();
    }

    private function populateQuestionRelationsTable()
    {
        // Inserting values
        foreach ($this->questionRelationships as $questionRelationship) {
            $this->insert('ophtroperationchecklists_question_relationships', $questionRelationship);
        }
    }

    private function createSectionTable()
    {
        // Creating Table to store the section name for a question so that it can be rendered
        $this->createOETable('ophtroperationchecklists_question_section', array(
            'id' => 'pk',
            'question_id' => 'int(11)',
            'section_name' => 'text',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationchecklists_qs_qid_fk',
            'ophtroperationchecklists_question_section',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->populateQuestionSectionsTable();
    }

    private function populateQuestionSectionsTable()
    {
        // Inserting values
        foreach ($this->questionSections as $questionSection) {
            $this->insert('ophtroperationchecklists_question_section', $questionSection);
        }
    }
}
