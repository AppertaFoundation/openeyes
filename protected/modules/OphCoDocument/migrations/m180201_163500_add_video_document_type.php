<?php

class m180201_163500_add_video_document_type extends OEMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $isVid = $this->dbConnection->createCommand()->select('id')->from('ophcodocument_sub_types')->where('name = :name', array(':name' => 'Video'))->queryRow();

        # Insert values if they don't already exist
        if ($isVid['id'] == '') {
            $this->insert('ophcodocument_sub_types', array(
                          'name' => 'Video',
                          'display_order' => '11',
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
