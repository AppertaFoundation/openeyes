<?php

class m191005_121000_preferred_method_communication extends OEMigration
{
    public function up()
    {
        $this->createOETable("ophcocvi_clericinfo_preferred_comm", [
            'id' => 'pk',
            'name' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
            'version' => 'int(10) unsigned NOT NULL DEFAULT 1',
        ], true);

        $this->insert('ophcocvi_clericinfo_preferred_comm', [
            'name' => 'BSL',
            'display_order'=> 10
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_comm', [
            'name' => 'Deafblind manual',
            'display_order'=> 20
        ]);


        $this->createOETable("ophcocvi_clericinfo_preferred_format", [
            'id' => 'pk',
            'name' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
            'version' => 'int(10) unsigned NOT NULL DEFAULT 1',
        ], true);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Large print 18',
            'display_order'=> 10
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Large print 22',
            'display_order'=> 20
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Large print 26',
            'display_order'=> 30
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Easy-Read',
            'display_order'=> 40
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Audio CD',
            'display_order'=> 50
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'Email',
            'display_order'=> 60
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_format', [
            'name' => 'I don’t know and need an assessment',
            'display_order'=> 70
        ]);

        $this->addOEColumn('et_ophcocvi_clericinfo', 'interpreter_required', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);
        $this->addOEColumn('et_ophcocvi_clericinfo', 'preferred_comm_id', 'int(10) DEFAULT NULL', true);
        $this->addOEColumn('et_ophcocvi_clericinfo', 'preferred_comm_other', 'text', true);

        $this->addForeignKey('fk_preferred_comm_id', 'et_ophcocvi_clericinfo', 'preferred_comm_id', 'ophcocvi_clericinfo_preferred_comm', 'id');

        $this->addOEColumn('et_ophcocvi_clericinfo', 'preferred_format_id', 'int(10) DEFAULT NULL', true);
        $this->addOEColumn('et_ophcocvi_clericinfo', 'preferred_format_other', 'text', true);

        $this->addForeignKey('fk_preferred_format_id', 'et_ophcocvi_clericinfo', 'preferred_format_id', 'ophcocvi_clericinfo_preferred_format', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_preferred_format_id', 'et_ophcocvi_clericinfo');
        $this->dropForeignKey('fk_preferred_comm_id', 'et_ophcocvi_clericinfo');

        $this->dropOEColumn('et_ophcocvi_clericinfo', 'preferred_format_other', true);
        $this->dropOEColumn('et_ophcocvi_clericinfo', 'preferred_format_id', true);
        $this->dropOEColumn('et_ophcocvi_clericinfo', 'preferred_comm_other', true);
        $this->dropOEColumn('et_ophcocvi_clericinfo', 'preferred_comm_id', true);
        $this->dropOEColumn('et_ophcocvi_clericinfo', 'interpreter_required', true);

        $this->dropOETable('ophcocvi_clericinfo_preferred_comm');
    }
}
