<?php

class m211025_154700_migrate_best_interest_decision extends OEMigration
{
    private const ARCHIVE_ET_BEST_INTEREST_TABLE = 'et_ophtrconsent_best_interest_decision_archive';
    private const ARCHIVE_ET_BEST_INTEREST_TABLE_V = 'et_ophtrconsent_best_interest_decision_version_archive';

    public function safeUp()
    {
        //DELETE
        /*if ($this->dbConnection->schema->getTable('et_ophtrconsent_best_interest_decision', true) !== null && !isset(Yii::app()->db->schema->getTable('et_ophtrconsent_best_interest_decision')->columns['deputy_granted'])) {

            $this->addOEColumn('et_ophtrconsent_best_interest_decision','deputy_granted','BOOLEAN',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision','circumstances','TEXT NULL',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision','imca_view','TEXT NULL',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision','options_less_restrictive','TEXT NULL',true);
            $this->addOEColumn('et_ophtrconsent_best_interest_decision','views_of_colleagues','TEXT NULL',true);
            $this->addOEColumn("et_ophtrconsent_best_interest_decision", "decision", "TEXT NULL");
            $this->addOEColumn("et_ophtrconsent_best_interest_decision", "no_decision_made", "BOOLEAN NOT NULL DEFAULT 0");
            
        }

        $this->dropTable(self::ARCHIVE_ET_BEST_INTEREST_TABLE);
        $this->dropTable(self::ARCHIVE_ET_BEST_INTEREST_TABLE_V);

        $this->execute("INSERT INTO `openeyes`.`et_ophtrconsent_best_interest_decision` (`event_id`, `patient_has_not_refused`, `reason_for_procedure`, `treatment_cannot_wait`, `treatment_cannot_wait_reason`, `wishes`, `decision`, `deputy_granted`, `circumstances`, `imca_view`, `options_less_restrictive`, `views_of_colleagues`) VALUES ('3686620', '1', 'Reason', '1', 'Cannot wait', 'Wish', 'Decision', '0', 'Circumstances', 'IMCA', 'Less restrictive', 'View of colleagues');");
        */ //DELETE


        if ($this->dbConnection->schema->getTable('et_ophtrconsent_best_interest_decision', true) !== null && isset(Yii::app()->db->schema->getTable('et_ophtrconsent_best_interest_decision')->columns['deputy_granted'])) {
            //Map data into new best interest decision element
            //$this->execute("CREATE TABLE " . self::ARCHIVE_ET_BEST_INTEREST_TABLE . " AS SELECT * FROM et_ophtrconsent_best_interest_decision");
            //$this->execute("CREATE TABLE " . self::ARCHIVE_ET_BEST_INTEREST_TABLE_V . " AS SELECT * FROM et_ophtrconsent_best_interest_decision_version");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision SET treatment_cannot_wait = 1 WHERE treatment_cannot_wait_reason IS NOT NULL");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision_version SET treatment_cannot_wait = 1 WHERE treatment_cannot_wait_reason IS NOT NULL;");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision SET wishes = concat(wishes, CHAR(10), options_less_restrictive) WHERE options_less_restrictive IS NOT NULL;");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision_version SET wishes = concat(wishes, CHAR(10), options_less_restrictive) WHERE options_less_restrictive IS NOT NULL;");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision SET reason_for_procedure = concat(reason_for_procedure, CHAR(10), circumstances) WHERE circumstances IS NOT NULL;");

            $this->execute("UPDATE et_ophtrconsent_best_interest_decision_version SET reason_for_procedure = concat(reason_for_procedure, CHAR(10), circumstances) WHERE circumstances IS NOT NULL;");

            

            //Create Capacity Advocat elements
            /*if($this->dbConnection->schema->getTable('et_ophtrconsent_medical_capacity_advocate', true) !== null) {
                $query_advocate = $this->dbConnection->createCommand('SELECT * FROM et_ophtrconsent_medical_capacity_advocate')->query();
                if($query_advocate->rowCount == 0) {
                    $capacity_advocate_instructed_yes_id = $this->dbConnection->createCommand('SELECT id FROM ophtrconsent_medical_capacity_advocate_instructed WHERE `name` = "Yes"')->queryScalar();
                    
                    $capacity_advocate_yes = $this->dbConnection->createCommand('SELECT * FROM et_ophtrconsent_best_interest_decision WHERE imca_view IS NOT NULL')->queryAll();

                    $this->insertMultiple('et_ophtrconsent_medical_capacity_advocate', array_map(
                        static function ($record) use ($capacity_advocate_instructed_yes_id) {
                            return array(
                                'event_id' => $record['event_id'],
                                'instructed_id' => $capacity_advocate_instructed_yes_id,
                                'outcome_decision' => $record['imca_view'],
                                'last_modified_user_id' => $record['last_modified_user_id'],
                                'last_modified_date' => $record['last_modified_date'],
                                'created_user_id' => $record['created_user_id'],
                                'created_date' => $record['created_date'],
                            );
                        },
                        $capacity_advocate_yes
                    ));
                }
            }


            //Create Patient Deputy elements
            if($this->dbConnection->schema->getTable('et_ophtrconsent_patient_attorney_deputy', true) !== null && $this->dbConnection->schema->getTable('ophtrconsent_patient_attorney_deputy_contact', true) !== null) {
                $query_deputy = $this->dbConnection->createCommand('SELECT * FROM et_ophtrconsent_best_interest_decision')->queryAll();
                    
                // Create deputy element 
                $this->insertMultiple('et_ophtrconsent_patient_attorney_deputy', array_map(
                    static function ($record) {
                        return array(
                            'event_id' => $record['event_id'],
                            'comments' => $record['deputy_granted'] == 1 ? 'Patient has been granted a health and welfare power of attorney or a deputy has been appointed: Yes' : 'Patient has been granted a health and welfare power of attorney or a deputy has been appointed: No',
                            'last_modified_user_id' => $record['last_modified_user_id'],
                            'last_modified_date' => $record['last_modified_date'],
                            'created_user_id' => $record['created_user_id'],
                            'created_date' => $record['created_date'],
                        );
                    },
                    $query_deputy
                ));

                $query_deputy_yes = $this->dbConnection->createCommand('SELECT * FROM et_ophtrconsent_best_interest_decision WHERE deputy_granted = 1')->query();
                if($query_deputy_yes->rowCount != 0) {
                    $power_of_attorney_contact_label_id = $this->dbConnection->createCommand('SELECT id FROM contact_label WHERE `name` = "Power of Attorney"')->queryScalar();
                    
                    $deputy_signature = $this->dbConnection->createCommand('SELECT * FROM et_ophtrconsent_best_interest_decision_deputy_signature')->queryAll();

                    //Add deputy contact if there is a signature
                    $this->insertMultiple('contact', 
                        $new_contacts = array_map(
                            static function ($record) use ($power_of_attorney_contact_label_id) {
                                $space_pos = strrpos($record['signatory_name']," ");

                                return array(
                                    'event_id' => $record['event_id'],
                                    'first_name' => substr($record['signatory_name'], 0, $space_pos+1),
                                    'last_name' => substr($record['signatory_name'], $space_pos+1),
                                    'primary_phone' => 9999999999,
                                    'mobile_phone' => 9999999999,
                                    'contact_label_id' => $power_of_attorney_contact_label_id,
                                    'created_institution_id' => Yii::app()->session['selected_institution_id'],
                                    'created_institution_id' => Yii::app()->session['selected_institution_id'],
                                    'active' => 1,
                                    'email' => 'moorfields.itservicedesk@nhs.net',
                                    'last_modified_user_id' => $record['last_modified_user_id'],
                                    'last_modified_date' => $record['last_modified_date'],
                                    'created_user_id' => $record['created_user_id'],
                                    'created_date' => $record['created_date'],
                                );
                            },
                            $deputy_signature
                        )
                    );

                    $first_contact_id = $this->dbConnection->getLastInsertID();
                    $row_count = 0;

                    foreach($new_contacts as $key => $new_contact) {
                        $episode_id = $this->dbConnection->createCommand('SELECT episode_id FROM `event` WHERE id = ' . $new_contact['event_id'])->queryScalar();
                            
                        $patient_id = $this->dbConnection->createCommand('SELECT patient_id FROM episode WHERE id = ' . $episode_id)->queryScalar();

                        $new_contacts[$key]['patient_id'] = $patient_id;

                        $new_contacts[$key]['contact_id'] = $first_contact_id + $row_count;

                        $row_count++;
                    }

                    if($this->dbConnection->schema->getTable('ophtrconsent_authorised_decision', true) !== null && $this->dbConnection->schema->getTable('ophtrconsent_considered_decision', true) !== null) {
                        $authorised_decision_id = $this->dbConnection->createCommand('SELECT id FROM ophtrconsent_authorised_decision WHERE name = "under a Lasting Power  of Attorney."')->queryScalar();
                        
                        $considered_decision_id = $this->dbConnection->createCommand('SELECT id FROM ophtrconsent_considered_decision WHERE name = "Yes"')->queryScalar();

                        $this->insertMultiple('ophtrconsent_patient_attorney_deputy_contact', array_map(
                            static function ($record) use ($authorised_decision_id, $considered_decision_id) {
                                return array(
                                    'patient_id' => $record['patient_id'],
                                    'contact_id' => $record['contact_id'],
                                    'authorised_decision_id' => $authorised_decision_id,
                                    'considered_decision_id' => $considered_decision_id,
                                    'event_id' => $record['event_id'],
                                );
                            },
                            $new_contacts
                        ));
                    }

                }
            }


            //Drop old columns
            /*$this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision
                            DROP COLUMN IF EXISTS deputy_granted,
                            DROP COLUMN IF EXISTS circumstances,
                            DROP COLUMN IF EXISTS imca_view,
                            DROP COLUMN IF EXISTS options_less_restrictive,
                            DROP COLUMN IF EXISTS decision,
                            DROP COLUMN IF EXISTS views_of_colleagues,
                            DROP COLUMN IF EXISTS no_decision_made');

            $this->execute('ALTER TABLE et_ophtrconsent_best_interest_decision_version
                            DROP COLUMN IF EXISTS deputy_granted,
                            DROP COLUMN IF EXISTS circumstances,
                            DROP COLUMN IF EXISTS imca_view,
                            DROP COLUMN IF EXISTS options_less_restrictive,
                            DROP COLUMN IF EXISTS decision,
                            DROP COLUMN IF EXISTS views_of_colleagues,
                            DROP COLUMN IF EXISTS no_decision_made');*/
        }

    }

    public function safeDown()
    {
        echo "m211025_154700_migrate_old_best_interest_decision_element does not support migration down.\n";
        return true;
    }


}
