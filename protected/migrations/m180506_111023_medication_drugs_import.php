<?php

class m180506_111023_medication_drugs_import extends OEMigration
{
    public function safeUp()
    {
        /*
         * table fixes
         */
        $this->execute("ALTER TABLE medication MODIFY source_subtype VARCHAR(45) NULL");

        // First check if column exists (for some reason it does in the Bolton database and possibly others)
        // if the columns already exist, Modify, if not, add
        if (isset($this->dbConnection->schema->getTable('medication_frequency')->columns['original_id'])) {
            $this->alterColumn('medication_frequency', 'original_id', 'INT NULL AFTER `code`');
        } else {
            // Add the column
            $this->addColumn('medication_frequency', 'original_id', 'INT NULL AFTER `code`');
        }

        // Then do the same for the version table (thse are done separetely to avoid issues where a column was previously added to the master, but not the version)
        if (isset($this->dbConnection->schema->getTable('medication_frequency_version')->columns['original_id'])) {
            $this->alterColumn('medication_frequency_version', 'original_id', 'INT NULL AFTER `code`');
        } else {
            // Add the column
            $this->addColumn('medication_frequency_version', 'original_id', 'INT NULL AFTER `code`');
        }

        $this->createIndex('fk_ref_medication_frequency_oidx', 'medication_frequency', 'original_id');

        /*
         * set medication_set and medication_set_rule tables
         */

        $usage_codes = [];
        $usage_codes_result = $this->dbConnection->createCommand()->select('id, usage_code')->from('medication_usage_code')->queryAll();
        foreach ($usage_codes_result as $item) {
            $usage_codes[$item['usage_code']] = $item['id'];
        }


        $drug_sets = $this->dbConnection
                ->createCommand('SELECT id, name, subspecialty_id FROM drug_set WHERE active=1 ORDER BY id ASC')
                ->queryAll();
        if ($drug_sets) {
            foreach ($drug_sets as $set) {
                $command = $this->dbConnection;
                $command->createCommand("INSERT INTO medication_set(name) values (:setname)")->execute(array(':setname' => $set['name']));
                $last_id = $command->getLastInsertID();
                $this->dbConnection->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id, subspecialty_id) values (" . $last_id . ", {$usage_codes["PRESCRIPTION_SET"]}, " . $set['subspecialty_id'] . " )")->execute();
            }

            $drug_sets = null;
            $command = null;
        }

        /* Set for formulary drugs */
        $this->dbConnection->createCommand("INSERT INTO medication_set(name) values ('Formulary')")->execute();
        $formulary_id = $this->dbConnection->getLastInsertID();

        $this->dbConnection->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id) values (" . $formulary_id . ", {$usage_codes['Formulary']})")->execute();

        /* Set for medication drugs */

        $this->dbConnection->createCommand("INSERT INTO medication_set(name) values ('Medication Drugs')")->execute();
        $medication_drugs_id = $this->dbConnection->getLastInsertID();

        /*
         * set medication_route table by drug_route table
         */

        $drugRoutesTable = 'drug_route';
        $drugRoutes = $this->dbConnection
                ->createCommand("SELECT CONCAT(id,'_drug_route') AS code, name FROM " . $drugRoutesTable . " ORDER BY id ASC")
                ->queryAll();

        if ($drugRoutes) {
            foreach ($drugRoutes as $route) {
                $command = $this->dbConnection
                ->createCommand("
                    INSERT INTO medication_route( term, code, source_type, source_subtype) 
                    values('" . $route['name'] . "' , '" . $route['code'] . "' ,'LEGACY', '" . $drugRoutesTable . "')
                ");
                $command->execute();
                $command = null;
            }
        }


        /*
         * set medication_form table by drug_form table
         */

        // Add active drug_forms
        $this->dbConnection->createCommand(
            "
            INSERT INTO medication_form( term, code, unit_term, default_dose_unit_term, source_type, source_subtype, deleted_date)
            SELECT  
                `name` as term, 
                CONCAT(id,'_drug_form') AS code, 
                `name` as unit_term, 
                `name` as default_dose_unit_term, 
                'LEGACY' as source_type, 
                'drug_form' as source_subtype, 
                CASE active WHEN 1 then NULL ELSE last_modified_date END as deleted_date
            FROM drug_form 
            WHERE active=1
            GROUP BY `name`
            ORDER BY id;
        "
        )->execute();

        // Add in active drug_forms - but only if there is not already an active form with the same name
        $this->dbConnection->createCommand(
            "
            INSERT INTO medication_form( term, code, unit_term, default_dose_unit_term, source_type, source_subtype, deleted_date)
            SELECT  
                `name` as term, 
                CONCAT(id,'_drug_form') AS code, 
                `name` as unit_term, 
                `name` as default_dose_unit_term, 
                'LEGACY' as source_type, 
                'drug_form' as source_subtype, 
                CASE active WHEN 1 then NULL ELSE last_modified_date END as deleted_date
            FROM drug_form 
            WHERE active=0
                AND `name` NOT IN (SELECT term FROM medication_form)
            GROUP BY `name`
            ORDER BY id;
        "
        )->execute();


        /*
         * set medication_frequency table by drug_frequency table
         */

        $drugFrequencyTable = 'drug_frequency';
        $drugFrequencies = $this->dbConnection
                ->createCommand("SELECT id AS original_id, name, CONCAT(id,'_drug_frequency') AS code, long_name FROM " . $drugFrequencyTable . " ORDER BY original_id ASC")
                ->queryAll();

        if ($drugFrequencies) {
            foreach ($drugFrequencies as $frequency) {
                $command = $this->dbConnection
                ->createCommand("
                    INSERT INTO medication_frequency( term, code , original_id ) 
                    values('" . $frequency['long_name'] . "' , '" . $frequency['name'] . "', " . $frequency['original_id'] . ")
                ");
                $command->execute();
                $command = null;
            }
        }

        /*
         * get and set medication_drug table data
         */

        $medication_drug_table = 'medication_drug';

        $this->dbConnection->createCommand("INSERT INTO medication(source_type, source_subtype, preferred_term, preferred_code, source_old_id)
        SELECT 'LEGACY', '" . $medication_drug_table . "', `name`, external_code, id FROM " . $medication_drug_table . " ORDER BY id ASC")->execute();

        $this->dbConnection->createCommand("INSERT INTO medication_set_item ( medication_id , medication_set_id )
        SELECT id, '" . $medication_drugs_id . "' FROM medication where source_subtype = '" . $medication_drug_table . "'")->execute();

        /*
         * get and set drug table data
         */

        $drugs_table = 'drug';
        $drugs = $this->dbConnection
                ->createCommand("
                    SELECT
                        d.id AS original_id, 
                        CONCAT(d.id,'_drug') AS drug_id, 
                        d.name,
                        d.tallman,
                        d.aliases,
                        d.form_id,
                        d.dose_unit,
                        d.default_frequency_id,
                        REGEXP_REPLACE(d.default_dose, '[a-z -\]', '' ) AS 'default_dose', -- strip non numerics
                        df.name AS drug_form_name,
                        rmf.id  AS ref_form_id,
                        rmf.default_dose_unit_term AS ref_dose_term,
                        rmr.id AS ref_route_id,           
                        rmfreq.id AS ref_freq_id,
                        d.default_duration_id
                    FROM " . $drugs_table . "               AS d
                    LEFT JOIN drug_form                 AS df           ON d.form_id = df.id
                    LEFT JOIN medication_form       AS rmf          ON rmf.default_dose_unit_term = df.name
                    LEFT JOIN drug_route                AS dr           ON d.default_route_id = dr.id 
                    LEFT JOIN medication_route      AS rmr          ON rmr.term  = dr.name
                    LEFT JOIN drug_frequency            AS dfreq        ON d.default_frequency_id = dfreq.id
                    LEFT JOIN medication_frequency  AS rmfreq       ON dfreq.id = rmfreq.original_id
                    ORDER BY original_id ASC
                 ")
                ->queryAll();


        if ($drugs) {
            foreach ($drugs as $drug) {
                $command = $this->dbConnection;
                $command->createCommand("
                          INSERT INTO medication(source_type, source_subtype, preferred_term, preferred_code, source_old_id, default_form_id, default_route_id, default_dose_unit_term) 
                        VALUES ('LEGACY', '" . $drugs_table . "', :drug_name, :source_old_id, :source_old_id, :default_form_id, :default_route_id, :default_dose_unit_term)
                    ")
                ->bindValue(':drug_name', $drug['name'])
                ->bindValue(':source_old_id', $drug['original_id'])
                ->bindValue(':default_form_id', $drug['ref_form_id'])
                ->bindValue(':default_route_id', $drug['ref_route_id'])
                ->bindValue(':default_dose_unit_term', $drug['dose_unit'])
                ->execute();
                $ref_medication_id = $command->getLastInsertID();

                $alternative_terms = [$drug['name']];

                foreach (explode(",", $drug['aliases']) as $alias) {
                    $alias = trim($alias);
                    if ($alias != "" && strcasecmp($alias, $drug['name']) !== 0) {
                        $alternative_terms[] = $alias;
                    }
                }

                foreach ($alternative_terms as $term) {
                    $this->execute("INSERT INTO medication_search_index (medication_id, alternative_term)
                                    VALUES
                                    (:id, :term)
                                    ", array(":id" => $ref_medication_id, ":term" => $term));
                }

                /* Add medication to the 'Formulary' set */
                $this->dbConnection->createCommand(
                    "
                    INSERT INTO medication_set_item( medication_id , medication_set_id, default_form_id, default_route_id, default_frequency_id, default_dose_unit_term )
                        values (:ref_medication_id , :formulary_id, NULL, :drug_route_id, :drug_freq_id , :default_dose_unit )
                "
                )
                ->bindValue(':ref_medication_id', $ref_medication_id)
                    ->bindValue(':formulary_id', $formulary_id)
                    ->bindValue(':drug_route_id', $drug['ref_route_id'])
                    ->bindValue(':drug_freq_id', $drug['ref_freq_id'])
                    ->bindValue(':default_dose_unit', $drug['dose_unit'])
                    ->execute();

                /* Add medication to their respective sets */
                $drug_sets = $this->dbConnection->createCommand(
                    "SELECT drug_set.id, `name`, subspecialty_id, dispense_condition_id, dispense_location_id, duration_id, dose, frequency_id
                FROM drug_set
                JOIN drug_set_item ON drug_set.id = drug_set_item.drug_set_id
                WHERE drug_set.active=1 AND drug_set_item.drug_id = :drug_id"
                )->bindValue(":drug_id", $drug['drug_id'])->queryAll();

                if ($drug_sets) {
                    foreach ($drug_sets as $drug_set) {
                        $this->dbConnection->createCommand("
                        INSERT INTO medication_set_item( medication_id , medication_set_id, default_form_id, default_dose, default_route_id, default_frequency_id, default_dose_unit_term, default_duration_id, default_dispense_condition_id, default_dispense_location_id)
                        values (:ref_medication_id ,
                            (SELECT id FROM medication_set WHERE `name` = :ref_set_name AND id IN 
                                (SELECT medication_set_id FROM medication_set_rule WHERE subspecialty_id = :subspecialty_id AND usage_code_id = :prescription_usage_code)
                            ),
                            NULL,
                            :default_dose,
                            :drug_route_id,
                            :defualt_freq_id,
                            :default_dose_unit,
                            :default_duration_id,
                            :dispense_condition_id,
                            :dispense_location_id
                            )
                ")
                            ->bindValue(':ref_medication_id', $ref_medication_id)
                            ->bindValue(':ref_set_name', $drug_set['name'])
                            ->bindValue(':subspecialty_id', $drug_set['subspecialty_id'])
                            ->bindValue(':prescription_usage_code', $usage_codes['PRESCRIPTION_SET'])
                            ->bindvalue(':default_dose', !empty(preg_replace('/[^\d.]+/', '', $drug_set['dose'])) ? preg_replace('/[^\d.]+/', '', $drug_set['dose']) : null)
                            ->bindValue(':drug_route_id', $drug['ref_route_id'])
                            ->bindValue(':defualt_freq_id', $drug_set['frequency_id'] ?: ($drug['default_frequency_id'] ?: $drug['ref_freq_id']))
                            ->bindValue(':default_dose_unit', $drug['dose_unit'])
                            ->bindValue(':default_duration_id', $drug_set['duration_id'] ?: $drug['default_duration_id'])
                            ->bindValue(':dispense_condition_id', $drug_set['dispense_condition_id'] ?: null)
                            ->bindValue(':dispense_location_id', $drug_set['dispense_location_id'] ?: null)
                            ->execute();
                    }
                }
            }

            $drugs = null;
            $command = null;
            $ref_medication_id = null;
        }
    }

    public function safeDown()
    {
        $this->dropIndex('fk_ref_medication_frequency_oidx', 'medication_frequency');

        $this->execute("ALTER TABLE medication_frequency DROP COLUMN original_id");
        $this->execute("ALTER TABLE medication_frequency_version DROP COLUMN original_id");
    }
}
