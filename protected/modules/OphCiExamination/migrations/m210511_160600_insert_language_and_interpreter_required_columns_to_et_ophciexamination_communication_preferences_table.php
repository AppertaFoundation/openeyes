<?php

class m210511_160600_insert_language_and_interpreter_required_columns_to_et_ophciexamination_communication_preferences_table extends OEMigration
{
    public function safeUp()
    {
        $table =$this->dbConnection->schema->getTable('patient', true);

        if (isset($table->columns['language_id']) && isset($table->columns['interpreter_required'])) {
            $existing_patients = $this->dbConnection->createCommand()
                ->select(
                    'id,
                    language_id,
                    interpreter_required'
                )
                ->from('patient')
                ->where('language_id IS NOT NULL')
                ->orWhere('interpreter_required IS NOT NULL')
                ->queryAll();

            $event_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');

            if (isset($existing_patients)) {
                foreach ($existing_patients as $existing_patient) {
                    $existing_episode = $this->dbConnection->createCommand()
                        ->select(
                            'id'
                        )
                        ->from('episode')
                        ->where('change_tracker = 1')
                        ->andWhere('patient_id= :existing_patient', array('existing_patient' => $existing_patient['id']))
                        ->queryRow();

                    if ($existing_episode == null) {
                        $this->insert('episode', array(
                            'patient_id' => $existing_patient['id'],
                            'start_date' => date('Y-m-d H:i:s'),
                            'last_modified_date' => date('Y-m-d H:i:s'),
                            'created_date' => date('Y-m-d H:i:s'),
                            'change_tracker' => 1,
                        ));

                        $episode_id = $this->dbConnection->getLastInsertID();
                    } else {
                        $episode_id = $existing_episode['id'];
                    }

                    $this->insert('event', array(
                        'episode_id' => $episode_id,
                        'event_type_id' => $event_type_id,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'event_date' => date('Y-m-d H:i:s'),
                        'institution_id' => 1,
                        'created_user_id' => 1 //admin
                    ));

                    $event_id = $this->dbConnection->getLastInsertID();

                    $this->insert('et_ophciexamination_communication_preferences', array(
                        'event_id' => $event_id,
                        'correspondence_in_large_letters' => 0,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'agrees_to_insecure_email_correspondence' => 0,
                        'language_id' => $existing_patient['language_id'],
                        'interpreter_required_id' => $existing_patient['interpreter_required'],
                    ));
                }
            }
        }

        if (isset($table->columns['language_id'])) {
            $this->dropOEColumn('patient', 'language_id', true);
        }

        if (isset($table->columns['interpreter_required'])) {
            $this->dropOEColumn('patient', 'interpreter_required', true);
        }
    }

    public function safeDown()
    {
        $table = $this->dbConnection->schema->getTable('patient', true);

        if (!isset($table->columns['language_id'])) {
            $this->addOEColumn(
                'patient',
                'language_id',
                'int(10) unsigned null',
                true
            );
        }

        if (!isset($table->columns['interpreter_required'])) {
            $this->addOEColumn(
                'patient',
                'interpreter_required',
                'int(10) unsigned null',
                true
            );
        }

        $table =$this->dbConnection->schema->getTable('et_ophciexamination_communication_preferences', true);

        if (isset($table->columns['language_id']) && isset($table->columns['interpreter_required_id'])) {
            $existing_patients = $this->dbConnection->createCommand()
                ->select(
                    'cp.id,
                    cp.language_id AS language_id ,
                    cp.interpreter_required_id AS interpreter_required_id,
                    ev.id AS eventId,
                    ep.id AS episodeId,
                    ep.patient_id AS patientId'
                )
                ->from('et_ophciexamination_communication_preferences cp')
                ->leftJoin('event ev', 'ev.id = cp.event_id')
                ->leftJoin('episode ep', 'ep.id = ev.episode_id')
                ->where('cp.language_id IS NOT NULL')
                ->orWhere('cp.interpreter_required_id IS NOT NULL')
                ->queryAll();

            foreach ($existing_patients as $existing_patient) {
                $this->update('patient', array('language_id' => $existing_patient['language_id'], 'interpreter_required' => $existing_patient['interpreter_required_id']), "id = " . $existing_patient['patientId']);
            }
        }
    }
}
