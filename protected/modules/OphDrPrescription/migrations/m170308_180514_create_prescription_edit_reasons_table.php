<?php

class m170308_180514_create_prescription_edit_reasons_table extends CDbMigration
{
    /*
    public function up()
    {

    }

    public function down()
    {

    }
    */


    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable('ophdrprescription_edit_reasons',
            array(
                'id' => 'pk',
                'caption' => 'string NOT NULL',
                'display_order' => 'tinyint(4) NOT NULL DEFAULT 0',
                'active'=>'tinyint(1) NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1'
            ));

        $this->addColumn('et_ophdrprescription_details', 'edit_reason_id', 'int NULL');
        $this->addColumn('et_ophdrprescription_details', 'edit_reason_other', 'varchar(256) NULL');
        $this->addColumn('et_ophdrprescription_details_version', 'edit_reason_id', 'int NULL');
        $this->addColumn('et_ophdrprescription_details_version', 'edit_reason_other', 'varchar(256) NULL');
        $this->addForeignKey(
            'et_ophdrprescription_details_edit_reason_fk',
            'et_ophdrprescription_details',
            'edit_reason_id',
            'ophdrprescription_edit_reasons',
            'id'
        );

        $this->insert('ophdrprescription_edit_reasons',
            array(
                'caption' => 'Other, please specify:',
                'display_order' => 99
            ));

        $this->insert('ophdrprescription_edit_reasons',
            array(
                'caption' => 'Incorrect drug prescribed - not dispensed',
                'display_order' => 1
            ));

        $this->insert('ophdrprescription_edit_reasons',
            array(
                'caption' => 'Saved too early, adding more drugs - not dispensed',
                'display_order' => 2
            ));

        $this->insert('ophdrprescription_edit_reasons',
            array(
                'caption' => 'Original drug not available, alternative dispensed',
                'display_order' => 3
            ));


    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'et_ophdrprescription_details_edit_reason_fk',
            'et_ophdrprescription_details');
        $this->dropColumn('et_ophdrprescription_details', 'edit_reason_id');
        $this->dropColumn('et_ophdrprescription_details', 'edit_reason_other');
        $this->dropColumn('et_ophdrprescription_details_version', 'edit_reason_id');
        $this->dropColumn('et_ophdrprescription_details_version', 'edit_reason_other');
        $this->dropTable('ophdrprescription_edit_reasons');
    }

}