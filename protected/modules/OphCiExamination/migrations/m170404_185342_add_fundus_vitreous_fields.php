<?php

class m170404_185342_add_fundus_vitreous_fields extends OEMigration
{
    protected static $vitreouses = array(
        'Syneresis',
        'PVD',
        'Pigment',
        'Weiss Ring'
    );
    public function up()
    {
        $this->createOETable('ophciexamination_vitreous', array(
            'id' => 'pk',
            'active' => 'boolean default true',
            'name' => 'varchar(255) NOT NULL',
            'display_order' => 'int(10) default 1',
        ));
        foreach (static::$vitreouses as $i => $vitreous) {
            $this->insert('ophciexamination_vitreous', array('name' => $vitreous, 'display_order' => $i));
        }
        $this->createOETable('ophciexamination_fundus_vitreous', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'vitreous_id' => 'int(11) NOT NULL',
            'side_id' => 'int(10) unsigned NOT NULL'
        ));
        $this->addForeignKey('ophciexamination_fundus_vitreous_el_fk', 'ophciexamination_fundus_vitreous',
            'element_id', 'et_ophciexamination_fundus', 'id');
        $this->addForeignKey('ophciexamination_fundus_vitreous_vit_fk', 'ophciexamination_fundus_vitreous',
            'vitreous_id', 'ophciexamination_vitreous', 'id');
        $this->addForeignKey('ophciexamination_fundus_vitreous_side_fk', 'ophciexamination_fundus_vitreous',
            'side_id', 'eye', 'id');

        $this->execute('update ophciexamination_vitreous set display_order = display_order + 1');
        $this->insert('ophciexamination_vitreous', array('name' => 'Formed, Attached', 'display_order' => 1));
    }
    public function down()
    {
        $this->dropOETable('ophciexamination_fundus_vitreous');
        $this->dropOETable('ophciexamination_vitreous');
        $this->delete('ophciexamination_vitreous', 'name = :name', array(':name' => 'Formed, Attached'));
        $this->execute('update ophciexamination_vitreous set display_order = display_order -1');
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