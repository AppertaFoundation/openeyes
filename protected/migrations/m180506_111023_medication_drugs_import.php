<?php

class m180506_111023_medication_drugs_import extends CDbMigration
{
    
    
   
	public function up()
	{
        /*
         * ref_ table fixes
         */
        $this->execute("ALTER TABLE ref_medication MODIFY source_subtype VARCHAR(45) NULL");
        $this->execute("ALTER TABLE ref_medication_frequency ADD COLUMN original_id INT(11) NULL AFTER `code`");
        $this->execute("ALTER TABLE ref_medication_frequency_version ADD COLUMN original_id INT(11) NULL AFTER `code`");
        
        $this->createIndex('fk_ref_medication_frequency_oidx', 'ref_medication_frequency', 'original_id');
        
        /* 
         * set ref_set and ref_set_rules table 
         */
       
        $drug_sets = Yii::app()->db
                ->createCommand('SELECT id, name, subspecialty_id FROM drug_set ORDER BY id ASC')
                ->queryAll();
        if($drug_sets){
            foreach($drug_sets as $set){
                $command = Yii::app()->db;
                $command->createCommand("INSERT INTO ref_set(name) values ('".$set['name']."')")->execute();
                $last_id = $command->getLastInsertID(); 
                Yii::app()->db->createCommand("INSERT INTO ref_set_rules(ref_set_id, usage_code, subspecialty_id) values (".$last_id.", 'Drug', ".$set['subspecialty_id']." )")->execute();
            }
            
            $drug_sets = null;
            $command = null;
        }
        
        Yii::app()->db->createCommand("INSERT INTO ref_set(name) values ('Drug Legacy')")->execute();
        $ref_set_ID = Yii::app()->db->createCommand("SELECT id FROM ref_set WHERE name = 'Drug Legacy' ")->queryRow();

        $legacy_set_id = $ref_set_ID['id'];

        Yii::app()->db->createCommand("INSERT INTO ref_set_rules(ref_set_id, usage_code) values (".$ref_set_ID['id'].", 'Drug')")->execute();
        Yii::app()->db->createCommand("INSERT INTO ref_set_rules(ref_set_id, usage_code) values (".$ref_set_ID['id'].", 'MedicationDrug')")->execute();
        
        
        /* 
         * set ref_medication_route table by drug_route table 
         */
        
        $drugRoutesTable = 'drug_route';
        $drugRoutes = Yii::app()->db
                ->createCommand("SELECT CONCAT(id,'_drug_route') AS code, name FROM ".$drugRoutesTable." ORDER BY id ASC")
                ->queryAll();
        
        if($drugRoutes){
            foreach($drugRoutes as $route){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO ref_medication_route( term, code, source_type, source_subtype) 
                    values('".$route['name']."' , '".$route['code']."' ,'LEGACY', '".$drugRoutesTable."')
                ");
                $command->execute();
                $command = null;
            }
        }
        
        
        /* 
         * set ref_medication_form table by drug_form table 
         */
        
        $drugFormTable = 'drug_form';
        $drugForms = Yii::app()->db
                ->createCommand("SELECT CONCAT(id,'_drug_form') AS code, name FROM ".$drugFormTable." ORDER BY id ASC")
                ->queryAll();
        
        if($drugForms){
            foreach($drugForms as $form){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO ref_medication_form( term, code, unit_term, default_dose_unit_term, source_type, source_subtype) 
                    values('".$form['name']."' ,'".$form['code']."', '".$form['name']."', '".$form['name']."', 'LEGACY', '".$drugFormTable."')
                ");
                $command->execute();
                $command = null;
            }
        }
        
        /* 
         * set ref_medication_frequency table by drug_frequency table 
         */
        
        $drugFrequencyTable = 'drug_frequency';
        $drugFrequencies = Yii::app()->db
                ->createCommand("SELECT id AS original_id, name, CONCAT(id,'_drug_frequency') AS code, long_name FROM ".$drugFrequencyTable." ORDER BY original_id ASC")
                ->queryAll();
        
        if($drugFrequencies){
            foreach($drugFrequencies as $frequency){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO ref_medication_frequency( term, code , original_id ) 
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
                ->createCommand("SELECT id AS original_id, CONCAT(id,'_medication_drug') AS id, name FROM ".$medication_drug_table." ORDER BY original_id ASC")
                ->queryAll();
        
        if($medication_drugs){
            foreach($medication_drugs as $drug){   
                $command = Yii::app()->db;
                $command->createCommand("
                        INSERT INTO ref_medication(source_type, source_subtype, preferred_term, preferred_code) 
                        values('LEGACY', '".$medication_drug_table."', :drug_name, :drug_code)
                    ")
                ->bindValue(':drug_name', $drug['name'])
                ->bindValue(':drug_code', $drug['id'])
                ->execute();
                
                $ref_medication_id = $command->getLastInsertID(); 
                
                Yii::app()->db->createCommand("
                    INSERT INTO ref_medication_set( ref_medication_id , ref_set_id )
                        values (".$ref_medication_id." , ".$ref_set_ID['id']." )
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
                    LEFT JOIN ref_medication_form       AS rmf          ON rmf.default_dose_unit_term = df.name
                    LEFT JOIN drug_route                AS dr           ON d.default_route_id = dr.id 
                    LEFT JOIN ref_medication_route      AS rmr          ON rmr.term  = dr.name
                    LEFT JOIN drug_frequency            AS dfreq        ON d.default_frequency_id = dfreq.id
                    LEFT JOIN ref_medication_frequency  AS rmfreq       ON dfreq.id = rmfreq.original_id
                    ORDER BY original_id ASC
                 ")
                ->queryAll();
        
        
        if($drugs){
           
            foreach($drugs as $drug){
        
                $command = Yii::app()->db;
                $command->createCommand("
                        INSERT INTO ref_medication(source_type, source_subtype, preferred_term, preferred_code) 
                        values('LEGACY', '".$drugs_table."', :drug_name, :drug_code)
                    ")
                ->bindValue(':drug_name', $drug['name'])
                ->bindValue(':drug_code', $drug['drug_id'])
                ->execute();
                $ref_medication_id = $command->getLastInsertID();

                $alternative_terms = [$drug['name']];

                if(!strcasecmp($drug['tallman'], $drug['name'])) {
                    $alternative_terms[]=$drug['tallman'];
                }

                foreach (explode(",", $drug['aliases']) as $alias) {
                    if(!strcasecmp($alias, $drug['name'])) {
                        $alternative_terms[]=$alias;
                    }
                }

                foreach ($alternative_terms as $term) {
                    $this->execute("INSERT INTO ref_medications_search_index (ref_medication_id, alternative_term)
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
                    INSERT INTO ref_medication_set( ref_medication_id , ref_set_id, default_form, default_route, default_frequency, default_dose_unit_term )
                        values (".$ref_medication_id." , ".$legacy_set_id.", ".$drug_form_id.", ".$drug_route_id.", ".$drug_freq_id." , '".$default_dose_unit."' )
                ")->execute();

                /* Add medication to their respective sets */
                $drug_sets = Yii::app()->db->createCommand("SELECT id, `name`, subspecialty_id FROM drug_set WHERE id IN (SELECT drug_set_id FROM drug_set_item WHERE drug_id = :drug_id)")->bindValue(":drug_id", $drug['drug_id'])->queryAll();
                if($drug_sets) {
                    foreach ($drug_sets as $drug_set) {
                        Yii::app()->db->createCommand("
                    INSERT INTO ref_medication_set( ref_medication_id , ref_set_id, default_form, default_route, default_frequency, default_dose_unit_term, default_duration )
                        values (".$ref_medication_id." ,
                         
                         (SELECT id FROM ref_set WHERE `name` = :ref_set_name AND id IN 
                            (SELECT ref_set_id FROM ref_set_rules WHERE subspecialty_id = :subspecialty_id AND usage_code = 'Drug') 
                         ),
                         
                         ".$drug_form_id.",
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
        $this->dropIndex('fk_ref_medication_frequency_oidx', 'ref_medication_frequency');
        
        $this->execute("ALTER TABLE ref_medication_frequency DROP COLUMN original_id");
        $this->execute("ALTER TABLE ref_medication_frequency_version DROP COLUMN original_id");
	}
}