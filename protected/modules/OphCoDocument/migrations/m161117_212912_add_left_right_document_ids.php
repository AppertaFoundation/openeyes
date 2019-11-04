<?php

class m161117_212912_add_left_right_document_ids extends OEMigration
{
    public function up()
    {
        $this->renameColumn('et_ophcodocument_document', 'document_id', 'single_document_id');
        $this->renameColumn('et_ophcodocument_document_version', 'document_id', 'single_document_id');
        $this->addColumn('et_ophcodocument_document', 'left_document_id', 'int(11)');
        $this->addColumn('et_ophcodocument_document_version', 'left_document_id', 'int(11)');
        $this->addColumn('et_ophcodocument_document', 'right_document_id', 'int(11)');
        $this->addColumn('et_ophcodocument_document_version', 'right_document_id', 'int(11)');

        $this->createOETable('ophcodocument_sub_types', array('id'=>'pk', 'name'=>'varchar(255)', 'display_order'=>'int(4)'), true);

        $this->insert('ophcodocument_sub_types', array('display_order'=>1, 'name'=>'General'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>2, 'name'=>'Biometry Report'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>3, 'name'=>'Referral Letter'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>4, 'name'=>'OCT'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>5, 'name'=>'Electrocardiogram'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>6, 'name'=>'Photograph'));
        $this->insert('ophcodocument_sub_types', array('display_order'=>7, 'name'=>'Consent Form'));

        $this->addColumn('event', 'sub_type', 'int(11)');
        $this->addColumn('event_version', 'sub_type', 'int(11)');

        $this->addColumn('et_ophcodocument_document', 'event_sub_type', 'int(11)');
        $this->addColumn('et_ophcodocument_document_version', 'event_sub_type', 'int(11)');

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