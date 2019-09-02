<?php

class m180506_111023_medication_drugs_import extends CDbMigration
{
    public function up()
	{
        /*
         * table fixes
         */
        $this->execute("ALTER TABLE medication MODIFY source_subtype VARCHAR(45) NULL");
        $this->execute("ALTER TABLE medication_frequency ADD COLUMN original_id INT(11) NULL AFTER `code`");
        $this->execute("ALTER TABLE medication_frequency_version ADD COLUMN original_id INT(11) NULL AFTER `code`");
        
        $this->createIndex('fk_ref_medication_frequency_oidx', 'medication_frequency', 'original_id');
        
        /* 
         * set medication_set and medication_set_rule tables
         */

        $usage_codes = [];
        $usage_codes_result = \Yii::app()->db->createCommand()->select('id, usage_code')->from('medication_usage_code')->queryAll();
        foreach ($usage_codes_result as $item) {
            $usage_codes[$item['usage_code']] = $item['id'];
        }


        $drug_sets = Yii::app()->db
                ->createCommand('SELECT id, name, subspecialty_id FROM drug_set ORDER BY id ASC')
                ->queryAll();
        if($drug_sets){
            foreach($drug_sets as $set){
                $command = Yii::app()->db;
                $command->createCommand("INSERT INTO medication_set(name) values ('".$set['name']."')")->execute();
                $last_id = $command->getLastInsertID(); 
                Yii::app()->db->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id, subspecialty_id) values (".$last_id.", {$usage_codes["Drug"]}, ".$set['subspecialty_id']." )")->execute();
            }
            
            $drug_sets = null;
            $command = null;
        }

        /* Set for formulary drugs */
        Yii::app()->db->createCommand("INSERT INTO medication_set(name) values ('Formulary')")->execute();
        $formulary_id = $this->dbConnection->getLastInsertID();

        /* Set for medication drugs */

        Yii::app()->db->createCommand("INSERT INTO medication_set(name) values ('Medication Drugs')")->execute();
        $medication_drugs_id = $this->dbConnection->getLastInsertID();

        Yii::app()->db->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id) values (".$formulary_id.", {$usage_codes['Drug']})")->execute();
        Yii::app()->db->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id) values (".$formulary_id.", {$usage_codes['Formulary']})")->execute();
        Yii::app()->db->createCommand("INSERT INTO medication_set_rule(medication_set_id, usage_code_id) values (".$medication_drugs_id.", {$usage_codes['MedicationDrug']})")->execute();

        /* 
         * set medication_route table by drug_route table
         */
        
        $drugRoutesTable = 'drug_route';
        $drugRoutes = Yii::app()->db
                ->createCommand("SELECT CONCAT(id,'_drug_route') AS code, name FROM ".$drugRoutesTable." ORDER BY id ASC")
                ->queryAll();
        
        if($drugRoutes){
            foreach($drugRoutes as $route){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO medication_route( term, code, source_type, source_subtype) 
                    values('".$route['name']."' , '".$route['code']."' ,'LEGACY', '".$drugRoutesTable."')
                ");
                $command->execute();
                $command = null;
            }
        }
        
        
        /* 
         * set medication_form table by drug_form table
         */
        
        $drugFormTable = 'drug_form';
        $drugForms = Yii::app()->db
                ->createCommand("SELECT CONCAT(id,'_drug_form') AS code, name FROM ".$drugFormTable." ORDER BY id ASC")
                ->queryAll();
        
        if($drugForms){
            foreach($drugForms as $form){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO medication_form( term, code, unit_term, default_dose_unit_term, source_type, source_subtype) 
                    values('".$form['name']."' ,'".$form['code']."', '".$form['name']."', '".$form['name']."', 'LEGACY', '".$drugFormTable."')
                ");
                $command->execute();
                $command = null;
            }
        }
        
        /* 
         * set medication_frequency table by drug_frequency table
         */
        
        $drugFrequencyTable = 'drug_frequency';
        $drugFrequencies = Yii::app()->db
                ->createCommand("SELECT id AS original_id, name, CONCAT(id,'_drug_frequency') AS code, long_name FROM ".$drugFrequencyTable." ORDER BY original_id ASC")
                ->queryAll();
        
        if($drugFrequencies){
            foreach($drugFrequencies as $frequency){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO medication_frequency( term, code , original_id ) 
                    values('".$frequency['long_name']."' , '".$frequency['name']."', ".$frequency['original_id'].")
                ");
                $command->execute();
                $command = null;
            }
        }
        
        /* 
         * get and set medication_drug table data 
         */
      
        $medication_drug_table = 'medication_drug';
        $medication_drugs = Yii::app()->db
                ->createCommand("SELECT id AS original_id, `name`, external_code FROM ".$medication_drug_table." ORDER BY original_id ASC")
                ->queryAll();
        
        if($medication_drugs){
            foreach($medication_drugs as $drug){   
                $command = Yii::app()->db;
                $command->createCommand("
                         INSERT INTO medication(source_type, source_subtype, preferred_term, preferred_code, source_old_id) 
                        values('LEGACY', '".$medication_drug_table."', :drug_name, :drug_code, :original_id)
                    ")
                ->bindValue(':drug_name', $drug['name'])
                    ->bindValue(':drug_code', $drug['external_code'])
                    ->bindValue(':original_id', $drug['original_id'])
                ->execute();
                
                $ref_medication_id = $command->getLastInsertID(); 
                
                Yii::app()->db->createCommand("
                    INSERT INTO medication_set_item( medication_id , medication_set_id )
                        values (".$ref_medication_id." , ".$medication_drugs_id." )
                ")->execute();
            }
            
            $command = null;
            $medication_drugs = null;
            $ref_medication_id = null;
        }
        
        /* 
         * get and set drug table data 
         */
         
        $drugs_table = 'drug';
        $drugs = Yii::app()->db
                ->createCommand("
                    SELECT
                        d.id AS original_id, 
                        CONCAT(d.id,'_drug') AS drug_id, 
                        d.name,
                        d.tallman,
                        d.aliases,
                        d.form_id,
                        d.dose_unit,
                        d.default_dose,
                        df.name AS drug_form_name,
                        rmf.id  AS ref_form_id,
                        rmf.default_dose_unit_term AS ref_dose_term,
                        rmr.id AS ref_route_id,           
                        rmfreq.id AS ref_freq_id,
                        d.default_duration_id
                    FROM ".$drugs_table."               AS d
                    LEFT JOIN drug_form                 AS df           ON d.form_id = df.id
                    LEFT JOIN medication_form       AS rmf          ON rmf.default_dose_unit_term = df.name
                    LEFT JOIN drug_route                AS dr           ON d.default_route_id = dr.id 
                    LEFT JOIN medication_route      AS rmr          ON rmr.term  = dr.name
                    LEFT JOIN drug_frequency            AS dfreq        ON d.default_frequency_id = dfreq.id
                    LEFT JOIN medication_frequency  AS rmfreq       ON dfreq.id = rmfreq.original_id
                    ORDER BY original_id ASC
                 ")
                ->queryAll();
        
        
        if($drugs){
           
            foreach($drugs as $drug){
        
                $command = Yii::app()->db;
                $command->createCommand("
                          INSERT INTO medication(source_type, source_subtype, preferred_term, preferred_code, source_old_id, default_form_id, default_route_id, default_dose_unit_term) 
                        VALUES ('LEGACY', '".$drugs_table."', :drug_name, '', :source_old_id, :default_form_id, :default_route_id, :default_dose_unit_term)
                    ")
                ->bindValue(':drug_name', $drug['name'])
                ->bindValue(':source_old_id', $drug['original_id'])
                ->bindValue(':default_form_id', $drug['ref_form_id'])
                ->bindValue(':default_route_id', $drug['ref_route_id'])
                ->bindValue(':default_dose_unit_term', $drug['dose_unit'])
                ->execute();
                $ref_medication_id = $command->getLastInsertID();

                $alternative_terms = [$drug['name']];

                $tallman = trim($drug['tallman']);

                if(!is_null($tallman) && $tallman != "" && strcasecmp($tallman, $drug['name']) !== 0) {
                    $alternative_terms[]=$tallman;
                }

                foreach (explode(",", $drug['aliases']) as $alias) {
                    $alias = trim($alias);
                    if($alias != "" && strcasecmp($alias, $drug['name']) !== 0) {
                        $alternative_terms[]=$alias;
                    }
                }

                foreach ($alternative_terms as $term) {
                    $this->execute("INSERT INTO medication_search_index (medication_id, alternative_term)
                                    VALUES
                                    (:id, :term)
                                    ", array(":id"=>$ref_medication_id, ":term" => $term));
                }

                $drug_form_id = ($drug['ref_form_id'] == null) ? 'NULL' : $drug['ref_form_id'];
                $drug_route_id = ($drug['ref_route_id'] == null) ? 'NULL' : $drug['ref_route_id'];
                $drug_freq_id = ($drug['ref_freq_id'] == null) ? 'NULL' : $drug['ref_freq_id'];
                $default_dose_unit = ($drug['dose_unit'] == null) ? 'NULL' : $drug['dose_unit'];
                $default_duration_id = ($drug['default_duration_id'] == null) ? 'NULL' : $drug['default_duration_id'];

                /* Add medication to the 'Legacy' set */
                Yii::app()->db->createCommand("
                    INSERT INTO medication_set_item( medication_id , medication_set_id, default_form_id, default_route_id, default_frequency_id, default_dose_unit_term )
                        values (".$ref_medication_id." , ".$formulary_id.", NULL, ".$drug_route_id.", ".$drug_freq_id." , '".$default_dose_unit."' )
                ")->execute();

                /* Add medication to their respective sets */
                $drug_sets = Yii::app()->db->createCommand("SELECT id, `name`, subspecialty_id FROM drug_set WHERE id IN (SELECT drug_set_id FROM drug_set_item WHERE drug_id = :drug_id)")->bindValue(":drug_id", $drug['drug_id'])->queryAll();
                if($drug_sets) {
                    foreach ($drug_sets as $drug_set) {
                        Yii::app()->db->createCommand("
                    INSERT INTO medication_set_item( medication_id , medication_set_id, default_form_id, default_route_id, default_frequency_id, default_dose_unit_term, default_duration_id)
                        values (".$ref_medication_id." ,
                         
                         (SELECT id FROM medication_set WHERE `name` = :ref_set_name AND id IN 
                            (SELECT medication_set_id FROM medication_set_rule WHERE subspecialty_id = :subspecialty_id AND usage_code_id = {$usage_codes['Drug']})
                         ),
                         
                         NULL,
                         ".$drug_route_id.",
                         ".$drug_freq_id." ,
                         '".$default_dose_unit."',
                          ".$default_duration_id."
                          )
                ")
                            ->bindValue(':ref_set_name', $drug_set['name'])
                            ->bindValue(':subspecialty_id', $drug_set['subspecialty_id'])
                            ->execute();
                    }
                }
            }
            
            $drugs = null;
            $command = null;
            $ref_medication_id = null;
        }
        
	}

	public function down()
	{
        $this->dropIndex('fk_ref_medication_frequency_oidx', 'medication_frequency');
        
        $this->execute("ALTER TABLE medication_frequency DROP COLUMN original_id");
        $this->execute("ALTER TABLE medication_frequency_version DROP COLUMN original_id");
	}
}