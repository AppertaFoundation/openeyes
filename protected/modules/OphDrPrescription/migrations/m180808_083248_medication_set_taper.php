<?php

class m180808_083248_medication_set_taper extends OEMigration
{
    public function up()
    {
        $this->createOETable('medication_set_item_taper', array(
            'id' => 'pk',
            'medication_set_item_id' => 'int NOT NULL',
            'dose' => 'FLOAT',
            'frequency_id' => 'INT NOT NULL',
            'duration_id' => 'INT NOT NULL'
        ), true);

        $this->addForeignKey('fk_rmst_med_id', 'medication_set_item_taper', 'medication_set_item_id', 'medication_set_item', 'id');
        $this->addForeignKey('fk_rmst_freq_id', 'medication_set_item_taper', 'frequency_id', 'medication_frequency', 'id');
        $this->addForeignKey('fk_rmst_duration_id', 'medication_set_item_taper', 'duration_id', 'medication_duration', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_rmst_med_id', 'medication_set_taper');
        $this->dropForeignKey('fk_rmst_freq_id', 'medication_set_taper');
        $this->dropForeignKey('fk_rmst_duration_id', 'medication_set_taper');
        $this->dropOETable('medication_set_taper', true);
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
