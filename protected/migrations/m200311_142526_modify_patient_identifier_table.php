<?php

class m200311_142526_modify_patient_identifier_table extends OEMigration
{

    private static $PATIENT_IDENTIFIER_TYPES = ['hos_num' => 'local', 'nhs_num' => 'global'];
    private static $MERGED_PATIENT_STATUS = PatientMergeRequest::STATUS_MERGED;

    public function getSettingValue($setting_name)
    {
        return $this->dbConnection->createCommand()->select('value')->from('setting_installation')
            ->where('`key` = "' . $setting_name . '"')
            ->queryScalar();
    }

    /**
     * Recursive function that stop whenever no more merge request where the given patient_id is not a primary patienet
     * in a merge while running it adds all patient identifiers to the patient that had never been merged in
     * @param $patient
     * @param $secondary_patient_id
     * @param $local_type_id
     * @param $global_type_id
     * @param $rows
     * @throws CException
     */
    public function insertPatientIdentifiersFromMergedPatients($patient, $secondary_patient_id, $local_type_id, $global_type_id, &$rows, $added_identifiers = [])
    {

        $patient_merged_in_merge_requests = $this->getDbConnection()
            ->createCommand("SELECT id, secondary_id,secondary_hos_num as hos_num, secondary_nhsnum as nhs_num
                            FROM patient_merge_request
                            WHERE primary_id = '$secondary_patient_id'  AND status = " . $this::$MERGED_PATIENT_STATUS)
            ->queryAll();

        foreach ($patient_merged_in_merge_requests as $patient_merged_in_merge_request) {
            foreach ($this::$PATIENT_IDENTIFIER_TYPES as $short_name => $type) {
                if ($patient_merged_in_merge_request[$short_name] &&
                    $patient_merged_in_merge_request[$short_name] !== $patient[$short_name]) {
                    $duplicate_identifier_found = false;
                    if (isset($added_identifiers[${$type . '_type_id'}])) {
                        foreach ($added_identifiers[${$type . '_type_id'}] as $identifier) {
                            if ($identifier === $patient_merged_in_merge_request[$short_name]) {
                                $duplicate_identifier_found = true;
                            }
                        }
                    }

                    if (!$duplicate_identifier_found) {
                        $rows[] = [
                            'patient_id' => $patient['id'],
                            'patient_identifier_type_id' => ${$type . '_type_id'},
                            'value' => $patient_merged_in_merge_request[$short_name],
                            'source_info' => "MERG ID =( " . $patient_merged_in_merge_request['id'] . ")",
                            'deleted' => 1,
                        ];
                        $added_identifiers[${$type . '_type_id'}][] = $patient_merged_in_merge_request[$short_name];
                    }
                }
            }
            $this->insertPatientIdentifiersFromMergedPatients($patient, $patient_merged_in_merge_request['secondary_id'], $local_type_id, $global_type_id, $rows, $added_identifiers);
        }
    }

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->dropForeignKey('patient_identifier_cui_fk', 'patient_identifier');
        $this->dropForeignKey('patient_identifier_lmui_fk', 'patient_identifier');
        $this->renameTable('patient_identifier', 'archive_patient_identifier');
        $this->renameTable('patient_identifier_version', 'archive_patient_identifier_version');
        $this->createOETable(
            'patient_identifier',
            array(
                'patient_id' => 'int(10) unsigned NOT NULL',
                'patient_identifier_type_id' => 'int(11) NOT NULL',
                'value' => 'varchar(255) NOT NULL',
                'source_info' => 'varchar(255) DEFAULT "ACTIVE" NOT NULL',
                'deleted' => 'tinyint(1) unsigned DEFAULT 0 NOT NULL',
            ),
            true
        );

        $this->execute("ALTER TABLE `patient_identifier` ADD `unique_row_str` varchar(255) GENERATED ALWAYS AS (CONCAT(patient_id,'-',patient_identifier_type_id,'-',IF (deleted = 0, 'ACTIVE', CONCAT(source_info,'-',patient_identifier.value))));");
        $this->execute("ALTER TABLE `patient_identifier_version` ADD `unique_row_str` varchar(255);");
        $this->createIndex('uk_patient_identifier_unique_row_str', 'patient_identifier', ['unique_row_str'], true);
        $this->addForeignKey('fk_patient_identifier_patient', 'patient_identifier', 'patient_id', 'patient', 'id');
        $this->createIndex('uk_patient_value_patient_type_id', 'patient_identifier', ['patient_identifier_type_id', 'value', 'source_info'], true);

        $this->createOETable(
            'patient_identifier_type',
            array(
                'id' => 'pk',
                'usage_type' => 'varchar(255)',
                'short_title' => 'varchar(255) NOT NULL',
                'long_title' => 'varchar(255) NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'site_id' => 'int(10) unsigned',
                'validate_regex' => 'varchar(255) NOT NULL',
                'extract_regex' => 'varchar(255) NULL',
                'value_display_prefix' => 'varchar(255)',
                'value_display_suffix' => 'varchar(255)',
                'pad' => 'varchar(255)',
                'pas_api' => 'TEXT'
            ),
            true
        );
        $this->addForeignKey('fk_patient_identifier_patient_identifier_type', 'patient_identifier', 'patient_identifier_type_id', 'patient_identifier_type', 'id');
        $this->addForeignKey('fk_patient_identifier_type_institution', 'patient_identifier_type', 'institution_id', 'institution', 'id');
        $this->createIndex('uk_patient_id_type_value', 'patient_identifier', 'patient_id,patient_identifier_type_id,value', true);

        $this->createIndex('uk_site_institution_index', 'site', 'id,institution_id', true);
        $this->addForeignKey(
            'fk_patient_identifier_type_site',
            'patient_identifier_type',
            'institution_id, site_id',
            'site',
            'institution_id,id'
        );

        $this->execute("ALTER TABLE `patient_identifier_type` ADD `unique_row_str` varchar(255) GENERATED ALWAYS AS (CONCAT(usage_type,'-',institution_id,'-',COALESCE(site_id,'0'))) COMMENT 'used for checking row is unique in table'");
        $this->execute("ALTER TABLE `patient_identifier_type_version` ADD `unique_row_str` varchar(255);");

        $this->createIndex('uk_unique_row_str', 'patient_identifier_type', ['unique_row_str'], true);

        $global_short_name = $this->getSettingValue("nhs_num_label_short");

        $global_name = "National Health Service";
        if (Yii::app()->params['default_country'] === 'Australia') {
            $global_name = "Medicare";
        }
        $this->insert('contact', ['active' => 1]);
        $this->execute("
                INSERT INTO institution (name, remote_id, short_name, contact_id, last_modified_date, created_date)
                VALUES ('{$global_name}'  , 'NHS', '{$global_short_name}', (SELECT id FROM contact ORDER BY id DESC LIMIT 1), '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "');
            ");

        foreach (['local', 'global'] as $usage_type) {
            if ($usage_type === 'local') {
                $institution_id = $this->dbConnection->createCommand()
                    ->select('id')
                    ->from('institution')
                    ->where('remote_id = :institution_code')
                    ->bindValues(array(':institution_code' => Yii::app()->params['institution_code']))
                    ->queryScalar();
                $validate_regex = Yii::app()->params['hos_num_regex'];
                $pad = Yii::app()->params['pad_hos_num'];
                $short_title = $this->getSettingValue("hos_num_label_short");
                $long_title = $this->getSettingValue("hos_num_label");
            } else {
                $institution_id = $this->getDbConnection()->createCommand("SELECT id FROM institution WHERE remote_id = 'NHS'")->queryScalar();
                $validate_regex = isset(Yii::app()->params['nhs_num_length']) ? '/^([0-9]{' . Yii::app()->params['nhs_num_length'] . '})$/i' : '/^([0-9]{3}[- ]?[0-9]{3}[- ]?[0-9]{4})$/i';
                $pad = null;
                $short_title = $global_short_name;
                $long_title = $global_name;
            }

            $to_insert_into_patient_identifier_type = [
                'usage_type' => strtoupper($usage_type),
                'short_title' => $short_title,
                'long_title' => $long_title,
                'institution_id' => $institution_id,
                'validate_regex' => $validate_regex,
                'pad' => $pad,
            ];

            $this->insert('patient_identifier_type', $to_insert_into_patient_identifier_type);
            /*$this->execute("
                INSERT INTO patient_identifier_type (usage_type, short_title,long_title, institution_id, validate_regex, pad, last_modified_date, created_date)
                VALUES (" . implode(",", $to_insert_into_patient_identifier_type) . ");
            ");*/
        }
        $local_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE usage_type = 'local'")->queryScalar();
        $global_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE usage_type = 'global'")->queryScalar();
        $genetics_installed = !empty($this->dbConnection->schema->getTable('genetics_patient'));
        if ($genetics_installed) {
            $genetics_patient_identifier_type = [
                'usage_type' => 'LOCAL',
                'short_title' => 'Genetics Subject ID',
                'long_title' => 'Genetics Subject ID',
                'institution_id' => $institution_id,
                'validate_regex' => '^\d*$',
                'pad' => null,
            ];

            $this->insert('patient_identifier_type', $genetics_patient_identifier_type);
        }
        $patients_count = $this->getDbConnection()->createCommand('SELECT COUNT(*) FROM patient')->queryScalar();
        if ($genetics_installed) {
            // If genetics module is enabled we filter out those patients who are genetics AND
            // no hos_num AND no nhs_num
            $patients_sql = 'SELECT p.id, hos_num, nhs_num, deleted
                             FROM patient p
                             WHERE p.id NOT IN (
                                    SELECT patient_id
                                    FROM genetics_patient
                                    WHERE (hos_num = "" OR hos_num IS NULL) AND (nhs_num = "" OR nhs_num IS NULL)
                               )';
        } else {
            $patients_sql = 'SELECT id, hos_num, nhs_num, deleted FROM patient';
        }

        // at this point, the query should return only those patients who has at least one number
        // (if its not genetics and no hos_num and nhs_num than the trust should address this issue)
        // later we grab all genetics patients and add them under a new local number

        // NOTE: Genetics patients with same hos_num or nhs_num are not filtered
        // This may help:
        // ./yiic duplicatepatients


        $pagination = new CPagination($patients_count);
        $pagination->pageSize = 1000;
        $dataProvider = new CSqlDataProvider($patients_sql, [
            'totalItemCount' => $patients_count,
            'sort' => [
                'attributes' => [
                    'id',
                ],
            ],
            'db' => $this->dbConnection,
        ]);
        $current_page = 0;

        // not to flood the logs
        echo "\n> insert into patient_identifier ... ";
        ob_start();

        while ($current_page < $pagination->getPageCount()) {
            $pagination->setCurrentPage($current_page);
            $dataProvider->setPagination($pagination);
            $patients = $dataProvider->getData(true);
            $rows = [];
            foreach ($patients as $patient) {
                $patient_id = $patient['id'];

                $patient_merged_to_merge_request_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_merge_request WHERE secondary_id = '$patient_id' AND status = " . $this::$MERGED_PATIENT_STATUS)->queryScalar();

                $source_info = (int)$patient['deleted'] === 0 ? 'ACTIVE' : "INACTIVE ID $patient_id";
                // check if patient id is secondary
                // if secondary that mean it was merged in
                // source_info = DELETED ID ( PATIENT MERGE ID )
                if ($patient_merged_to_merge_request_id) {
                    $source_info = "DEL ID =(" . $patient_merged_to_merge_request_id . ")";
                } else {
                    // Check if patient had other patients merged into it
                    $patient_merged_in_merge_requests = $this->getDbConnection()
                        ->createCommand("SELECT id, secondary_id,secondary_hos_num as hos_num, secondary_nhsnum as nhs_num FROM patient_merge_request WHERE primary_id = '$patient_id' AND status = " . $this::$MERGED_PATIENT_STATUS)
                        ->queryAll();
                    $added_identifiers = [];
                    // Go through all the merge requests and add identifiers to the patient
                    foreach ($patient_merged_in_merge_requests as $patient_merged_in_merge_request) {
                        foreach ($this::$PATIENT_IDENTIFIER_TYPES as $short_name => $type) {
                            if ($patient_merged_in_merge_request[$short_name]
                                && $patient[$short_name] !== $patient_merged_in_merge_request[$short_name]) {
                                $duplicate_identifier_found = false;
                                if (isset($added_identifiers[${$type . '_type_id'}])) {
                                    foreach ($added_identifiers[${$type . '_type_id'}] as $identifier) {
                                        if ($identifier === $patient_merged_in_merge_request[$short_name]) {
                                            $duplicate_identifier_found = true;
                                        }
                                    }
                                }

                                if (!$duplicate_identifier_found) {
                                    $rows[] = [
                                        'patient_id' => $patient['id'],
                                        'patient_identifier_type_id' => ${$type . '_type_id'},
                                        'value' => $patient_merged_in_merge_request[$short_name],
                                        'source_info' => "MERG ID = (" . $patient_merged_in_merge_request['id'] . ")",
                                        'deleted' => 1,
                                    ];

                                    $added_identifiers[${$type . '_type_id'}][] = $patient_merged_in_merge_request[$short_name];
                                }
                            }
                        }

                        // Recursive function that will add patient identifiers from patients that was merged into
                        // secondary patients
                        $this->insertPatientIdentifiersFromMergedPatients($patient,
                            $patient_merged_in_merge_request['secondary_id'],
                            $local_type_id,
                            $global_type_id,
                            $rows,
                            $added_identifiers);
                    }
                }

                foreach ($this::$PATIENT_IDENTIFIER_TYPES as $short_name => $type) {
                    if ($patient[$short_name]) {
                        $rows[] = [
                            'patient_id' => $patient['id'],
                            'patient_identifier_type_id' => ${$type . '_type_id'},
                            'value' => $patient[$short_name],
                            'source_info' => $source_info,
                            'deleted' => $patient['deleted'],
                        ];
                    }
                }
            }
            if (!empty($rows)) {
                $this->insertMultiple('patient_identifier', $rows);
            }
            $current_page++;
        }

        ob_get_clean();

        if (Yii::app()->hasModule('OphGenetics')) {
            $genetics_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE short_title = 'Genetics Subject ID'")->queryScalar();
            $command = Yii::app()->db->createCommand()
                ->select('genetics_patient.id as genetics_patient_id, p.id as patient_id')
                ->from('genetics_patient')
                ->join('patient p', 'p.id = genetics_patient.patient_id');

            $iterator = new QueryIterator($command, $by = 2000);
            foreach ($iterator as $chunk) {
                $rows = [];
                foreach ($chunk as $genetics_patient) {
                    $rows[] = [
                        'patient_id' => $genetics_patient['patient_id'],
                        'patient_identifier_type_id' => $genetics_type_id,
                        'value' => $genetics_patient['genetics_patient_id'],
                    ];
                }
                $this->insertMultiple('patient_identifier', $rows);
            }
        }

        echo "done\n";
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_patient_identifier_type_site', 'patient_identifier_type');
        $this->dropForeignKey('fk_patient_identifier_type_institution', 'patient_identifier_type');
        $this->dropForeignKey('fk_patient_identifier_patient_identifier_type', 'patient_identifier');
        $this->dropIndex('uk_unique_row_str', 'patient_identifier_type');
        $this->dropOETable('patient_identifier_type', true);

        $this->delete('institution', 'name = ?', array('NHS'));

        $this->dropForeignKey('fk_patient_identifier_patient', 'patient_identifier');
        $this->dropIndex('uk_site_institution_index', 'site');
        $this->dropIndex('uk_unique_row_str', 'patient_identifier');
        $this->dropOETable('patient_identifier', true);

        $this->renameTable('archive_patient_identifier', 'patient_identifier');
        $this->renameTable('archive_patient_identifier_version', 'patient_identifier_version');
        $this->addForeignKey('patient_identifier_cui_fk', 'patient_identifier', 'created_user_id', 'user', 'id');
        $this->addForeignKey('patient_identifier_lmui_fk', 'patient_identifier', 'last_modified_user_id', 'user', 'id');
    }
}
