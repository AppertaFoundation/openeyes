<?php

class m180315_140200_add_ultrasound_retcam_document_type extends OEMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $isUS = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Ultrasound'))->queryRow();
        $isRC = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Retcam'))->queryRow();

        # Insert values if they don't already exist
        if ($isUS['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Ultrasound',
                          'display_order' => '30',
                  ));
        }

        # Insert values if they don't already exist
        if ($isRC['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Retcam',
                          'display_order' => '20',
                  ));
        }
    }

    public function down()
    {
        echo "Not supported here!\n";
        return true;
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
