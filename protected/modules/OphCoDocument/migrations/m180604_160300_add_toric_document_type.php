<?php

class m180604_160300_add_toric_document_type extends OEMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $isTypeExist = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Toric IOL Calculation'))->queryRow();

        # Insert values if they don't already exist
        if ($isTypeExist['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Toric IOL Calculation',
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
