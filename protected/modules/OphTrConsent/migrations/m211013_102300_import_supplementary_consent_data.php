<?php

class m211013_102300_import_supplementary_consent_data extends OEMigration
{
    const PERMISSION_TBL = "et_ophtrconsent_permissions";
    const PERMISSION_IMAGES_TBL = "ophtrconsent_permissions_images";
    const NEW_ET_TBL = "et_ophtrconsent_sup_consent_element";
    const NEW_QUESTION_TBL = "ophtrconsent_sup_consent_element_question";
    const NEW_ANSWER_TBL = "ophtrconsent_sup_consent_element_question_answer";
    const NEW_ADMIN_QUESTION_TBL = "ophtrconsent_sup_consent_question";
    const NEW_ADMIN_QUESTION_ASSIGN_TBL = "ophtrconsent_sup_consent_question_assignment";
    const NEW_ADMIN_ANSWER_TBL = "ophtrconsent_sup_consent_question_answer";

    public function up()
    {
        $question_id = $this->createQuestionInAdmin();
        if (isset($question_id)) {
            $this->migrateOldData($question_id);
        }
    }

    public function safeDown()
    {
        if ($this->dbConnection->schema->getTable(self::NEW_ADMIN_QUESTION_TBL) && $this->dbConnection->schema->getTable(self::NEW_ANSWER_TBL) && $this->dbConnection->schema->getTable(self::NEW_QUESTION_TBL) && $this->dbConnection->schema->getTable(self::NEW_ET_TBL) && $this->dbConnection->schema->getTable(self::PERMISSION_TBL)) {
            $question_id = $this->dbConnection
                ->createCommand("
				SELECT id FROM " . self::NEW_ADMIN_QUESTION_TBL . " WHERE `name` = 'Permissions for images'
				")->queryScalar();

            $this->execute("
        DELETE FROM " . self::NEW_ANSWER_TBL . "
        WHERE element_question_id IN (SELECT a.id FROM " . self::NEW_QUESTION_TBL . " as a
        RIGHT JOIN " . self::NEW_ET_TBL . " AS b ON a.element_id = b.id
        RIGHT JOIN " . self::PERMISSION_TBL . " AS c ON b.event_id = c.event_id)
        ");

            $this->delete(self::NEW_QUESTION_TBL, 'question_id = ' . $question_id);
            $this->execute("
        DELETE FROM " . self::NEW_ET_TBL . "
        WHERE event_id IN (SELECT a.event_id FROM " . self::PERMISSION_TBL . " as a)
        ");

            $this->delete(self::NEW_ADMIN_ANSWER_TBL, 'question_assignment_id = ' . $question_id);
            $this->delete(self::NEW_ADMIN_QUESTION_ASSIGN_TBL, 'question_id = ' . $question_id);
            $this->delete(self::NEW_ADMIN_QUESTION_TBL, 'id = ' . $question_id);
        }
    }

    private function migrateOldData($question_id) {
        if ($this->dbConnection->schema->getTable(self::NEW_ANSWER_TBL) && $this->dbConnection->schema->getTable(self::NEW_QUESTION_TBL) && $this->dbConnection->schema->getTable(self::NEW_ET_TBL) && $this->dbConnection->schema->getTable(self::PERMISSION_TBL) && $this->dbConnection->schema->getTable(self::PERMISSION_TBL) && $this->dbConnection->schema->getTable(self::NEW_ADMIN_ANSWER_TBL) && $this->dbConnection->schema->getTable(self::PERMISSION_IMAGES_TBL)) {
            $this->execute("
                INSERT INTO " . self::NEW_ET_TBL . " (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT event_id, last_modified_user_id, last_modified_date, created_user_id, created_date
                FROM " . self::PERMISSION_TBL . "
                ");

            $this->execute("
                INSERT INTO " . self::NEW_QUESTION_TBL . " (element_id, question_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT b.id, " . $question_id . ", a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                FROM " . self::PERMISSION_TBL . " as a
                LEFT JOIN " . self::NEW_ET_TBL . " as b ON a.event_id = b.event_id
                ");

            $this->execute("
                INSERT INTO " . self::NEW_ANSWER_TBL . " (element_question_id, answer_id, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT a.id, f.id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date
                FROM " . self::NEW_QUESTION_TBL . " as a
                LEFT JOIN " . self::NEW_ET_TBL . " as b ON a.element_id = b.id
                LEFT JOIN " . self::PERMISSION_TBL . " as c ON b.event_id = c.event_id
                LEFT JOIN " . self::NEW_ADMIN_ANSWER_TBL . " as d ON a.question_id = d.question_assignment_id
                LEFT JOIN " . self::PERMISSION_IMAGES_TBL . " as e ON c.images_id = e.id
                LEFT JOIN " . self::NEW_ADMIN_ANSWER_TBL . " as f ON e.name = f.display
                WHERE e.name IS NOT NULL GROUP BY a.id
                ");
        }
    }

    private function createQuestionInAdmin() {
        if ($this->dbConnection->schema->getTable(self::NEW_ADMIN_QUESTION_TBL) && $this->dbConnection->schema->getTable(self::NEW_ADMIN_QUESTION_ASSIGN_TBL) && $this->dbConnection->schema->getTable(self::NEW_ADMIN_ANSWER_TBL)) {
            $this->execute("INSERT INTO " . self::NEW_ADMIN_QUESTION_TBL . "(
        question_type_id,
        `name`,
        description,
        last_modified_user_id,
        last_modified_date,
        created_user_id,
        created_date
        )
        VALUES (
        1,
        'Permissions for images',
        'Agree for use an audit, education and publication',
        1,
        CURRENT_DATE(),
        1,
        CURRENT_DATE());
        ");

            $question_id = $this->getDbConnection()->getLastInsertID();

            $this->execute("INSERT INTO " . self::NEW_ADMIN_QUESTION_ASSIGN_TBL . "(
        question_id,
        question_text,
        question_info,
        required,
        active,
        last_modified_user_id,
        last_modified_date,
        created_user_id,
        created_date
        )
        VALUES (
        " . $question_id . ",
        'Permissions for images',
        'Agree for use an audit, education and publication',
        1,
        1,
        1,
        CURRENT_DATE(),
        1,
        CURRENT_DATE());
        ");

            $this->insertMultiple(
                self::NEW_ADMIN_ANSWER_TBL,
                [
                    [
                        'question_assignment_id' => $question_id,
                        'display_order' => '1',
                        'name' => 'yes',
                        'display' => 'Yes',
                        'answer_output' => 'Yes',
                        'last_modified_user_id' => 1,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_user_id' => 1,
                        'created_date' => date('Y-m-d H:i:s')
                    ],
                    [
                        'question_assignment_id' => $question_id,
                        'display_order' => '2',
                        'name' => 'no',
                        'display' => 'No',
                        'answer_output' => 'No',
                        'last_modified_user_id' => 1,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_user_id' => 1,
                        'created_date' => date('Y-m-d H:i:s')
                    ],
                    [
                        'question_assignment_id' => $question_id,
                        'display_order' => '3',
                        'name' => 'not applicable',
                        'display' => 'Not applicable',
                        'answer_output' => 'Not applicable',
                        'last_modified_user_id' => 1,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_user_id' => 1,
                        'created_date' => date('Y-m-d H:i:s')
                    ],
                ]
            );

            return $question_id;
        }
    }
}
