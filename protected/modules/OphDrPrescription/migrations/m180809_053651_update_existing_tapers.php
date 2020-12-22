<?php

class m180809_053651_update_existing_tapers extends CDbMigration
{
    public function up()
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->dropForeignKey('ophdrprescription_item_taper_frequency_id_fk', 'ophdrprescription_item_taper');
            $this->dropForeignKey('ophdrprescription_item_taper_duration_id_fk', 'ophdrprescription_item_taper');
            $this->execute("ALTER TABLE ophdrprescription_item_taper MODIFY frequency_id INT NOT NULL");
            $this->execute("ALTER TABLE ophdrprescription_item_taper MODIFY duration_id INT NOT NULL");
            $this->execute("UPDATE ophdrprescription_item_taper 
                                LEFT JOIN medication_frequency ON medication_frequency.id = ophdrprescription_item_taper.frequency_id  
                                SET ophdrprescription_item_taper.frequency_id = medication_frequency.original_id
                                ");
            $this->addForeignKey(
                'ophdrprescription_item_taper_frequency_id_fk',
                'ophdrprescription_item_taper',
                'frequency_id',
                'medication_frequency',
                'id'
            );
            $this->addForeignKey(
                'ophdrprescription_item_taper_duration_id_fk',
                'ophdrprescription_item_taper',
                'duration_id',
                'medication_duration',
                'id'
            );
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            $transaction->rollback();
            return false;
        }

        $transaction->commit();

        return true;
    }

    public function down()
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->dropForeignKey('ophdrprescription_item_taper_frequency_id_fk', 'ophdrprescription_item_taper');
            $this->dropForeignKey('ophdrprescription_item_taper_duration_id_fk', 'ophdrprescription_item_taper');
            $this->execute("ALTER TABLE ophdrprescription_item_taper MODIFY frequency_id INT(10) UNSIGNED NOT NULL");
            $this->execute("ALTER TABLE ophdrprescription_item_taper MODIFY duration_id INT(10) UNSIGNED NOT NULL");
            $this->execute("UPDATE ophdrprescription_item_taper 
                                LEFT JOIN  medication_frequency ON medication_frequency.original_id = ophdrprescription_item_taper.frequency_id  
                                SET ophdrprescription_item_taper.frequency_id = medication_frequency.id
                                ");
            $this->addForeignKey(
                'ophdrprescription_item_taper_frequency_id_fk',
                'ophdrprescription_item_taper',
                'frequency_id',
                'drug_frequency',
                'id'
            );

            $this->addForeignKey(
                'ophdrprescription_item_taper_duration_id_fk',
                'ophdrprescription_item_taper',
                'duration_id',
                'drug_duration',
                'id'
            );
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            $transaction->rollback();
            return false;
        }

        $transaction->commit();

        return true;
    }
}
