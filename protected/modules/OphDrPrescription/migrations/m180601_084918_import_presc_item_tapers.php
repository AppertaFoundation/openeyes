<?php

class m180601_084918_import_presc_item_tapers extends CDbMigration
{
	public function up()
	{
        
        $this->addColumn('ophdrprescription_item_taper', 'event_medication_uses_id', 'int(11) DEFAULT NULL AFTER item_id' );  
        $this->createIndex('idx_event_med_uses_id', 'ophdrprescription_item_taper', 'event_medication_uses_id');      
        $this->addForeignKey('fk_event_med_uses_id', 'ophdrprescription_item_taper', 'event_medication_uses_id', 'event_medication_uses', 'id');
        
        $this->addColumn('ophdrprescription_item_taper_version', 'event_medication_uses_id', 'int(11) DEFAULT NULL AFTER item_id' );  
        $this->createIndex('idx_event_med_uses_id', 'ophdrprescription_item_taper_version', 'event_medication_uses_id');      
        
        $tapers = Yii::app()->db->createCommand("
            SELECT 
                presc_it.id     AS id,
                emu.id  AS event_medication_uses_id
            FROM ophdrprescription_item_taper   AS presc_it
            JOIN event_medication_uses          AS emu      ON  presc_it.item_id = emu.temp_prescription_item_id 
        ")->queryAll();
        
        if($tapers){
            foreach($tapers as $taper){
            
                $command = Yii::app()->db
                ->createCommand("
                    UPDATE ophdrprescription_item_taper
                    SET event_medication_uses_id = ".$taper['event_medication_uses_id']."
                    WHERE id = ".$taper['id']."
                ");
                
                $command->execute();
                $command = null;
            }
        }
	}

	public function down()
	{
        $this->dropIndex('idx_event_med_uses_id', 'ophdrprescription_item_taper_version');
        $this->dropColumn('ophdrprescription_item_taper_version', 'event_medication_uses_id');
        
        $this->dropForeignKey('fk_event_med_uses_id', 'ophdrprescription_item_taper');
        $this->dropIndex('idx_event_med_uses_id', 'ophdrprescription_item_taper');
        $this->dropColumn('ophdrprescription_item_taper', 'event_medication_uses_id');
	}
}