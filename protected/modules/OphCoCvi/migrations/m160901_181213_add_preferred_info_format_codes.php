<?php

class m160901_181213_add_preferred_info_format_codes extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'code', 'varchar(15)');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'code', 'varchar(15)');

        $this->update('ophcocvi_clericinfo_preferred_info_fmt', array('code' => 'LGPRINT'), 'name = :name',
            array(':name' => 'In large print'));
        $this->update('ophcocvi_clericinfo_preferred_info_fmt', array('code' => 'CD'), 'name = :name',
            array(':name' => 'On CD'));
        $this->update('ophcocvi_clericinfo_preferred_info_fmt', array('code' => 'BRAILLE'), 'name = :name',
            array(':name' => 'In braille'));
        $this->update('ophcocvi_clericinfo_preferred_info_fmt', array('code' => 'EMAIL'), 'name = :name',
            array(':name' => 'By email'));

    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'code');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'code');
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