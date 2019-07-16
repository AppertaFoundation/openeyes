<?php

class m171211_134000_add_more_document_types extends OEMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $isVF = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Visual Field Report'))->queryRow();
        $isLid = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Lids Photo'))->queryRow();
        $isOrb = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Orbit Photo'))->queryRow();

        # Insert values if they don't already exist
        if ($isVF['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Visual Field Report',
                          'display_order' => '8',
                  ));
        }

        if ($isLid['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Lids Photo',
                          'display_order' => '9',
                  ));
        }

        if ($isOrb['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Orbit Photo',
                          'display_order' => '10',
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
