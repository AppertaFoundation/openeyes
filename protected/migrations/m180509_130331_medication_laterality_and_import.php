<?php

class m180509_130331_medication_laterality_and_import extends OEMigration
{
	public function up()
	{
        
        $this->createOETable('medication_laterality', array(
            'id'                    => 'pk',
            'name'                  => 'VARCHAR(40) NOT NULL',
            'deleted_date'          => 'DATE NULL',
        ), true);
        
        $drug_route_options = Yii::app()->db
                ->createCommand('SELECT name FROM drug_route_option GROUP BY name ORDER BY id  ASC')
                ->queryAll();
        
        if ($drug_route_options){
            foreach($drug_route_options as $option){
                $command = Yii::app()->db
                ->createCommand("
                    INSERT INTO medication_laterality( name) 
                    values('".$option['name']."' )
                ");
                $command->execute();
                $command = null;
            }
        }
	}

	public function down()
	{
		$this->dropOETable('medication_laterality', true);
	}

}