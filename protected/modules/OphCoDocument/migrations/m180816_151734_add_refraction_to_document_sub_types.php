<?php

class m180816_151734_add_refraction_to_document_sub_types extends CDbMigration
{
    public function up()
    {
        $this->insert('ophcodocument_sub_types', array('display_order'=>12, 'name'=>'Refraction'));
    }

    public function down()
    {
        $this->delete('ophcodocument_sub_types', 'name = ?', array('Refraction'));
    }
}