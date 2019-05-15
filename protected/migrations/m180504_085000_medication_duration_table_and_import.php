<?php

class m180504_085000_medication_duration_table_and_import extends OEMigration
{
	public function up()
	{
        $this->createOETable('medication_duration', array(
            'id'                    => 'pk',
            'name'                  => 'VARCHAR(40) NOT NULL',
            'display_order'         => 'INT NULL',
            'deleted_date'          => 'DATE NULL',
        ), true);
        
        $drug_durations = Yii::app()->db
                ->createCommand('SELECT id, name, display_order FROM drug_duration ORDER BY id ASC')
                ->queryAll();
        if($drug_durations){
            foreach($drug_durations as $duration){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO medication_duration(id, `name`, display_order) 
                    values('".$duration['id']."', '".$duration['name']."' , ".$duration['display_order']." )
                ");
                $command->execute();
                $command = null;
            }
        }
	}

	public function down()
	{
        $this->dropOETable('medication_duration', true);
	}

}