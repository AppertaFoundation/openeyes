<?php

class m191225_045407_create_groups_for_systemic_disorders extends OEMigration
{
    public function up()
    {
        $this->createOETable('common_systemic_disorder_group', [
            'id' => 'pk',
            'name' => 'varchar(64) not null',
            'display_order' => 'tinyint(1) unsigned not null',
        ], true);

        $this->addOEColumn('common_systemic_disorder', 'display_order', 'int default 1', true);
        $this->addOEColumn('common_systemic_disorder', 'group_id', 'int NULL', true);
        $this->createIndex('common_systemic_disorder_group_id_fk', 'common_systemic_disorder', 'group_id');
        $this->addForeignKey('common_systemic_disorder_group_id_fk', 'common_systemic_disorder', 'group_id', 'common_systemic_disorder_group', 'id');

        //set display_order
        $disorders = CommonSystemicDisorder::model()->findAll();
        foreach ($disorders as $disorder) {
            $this->update('common_systemic_disorder', ['display_order' => $disorder->id], "id=$disorder->id");
        }
    }

    public function down()
    {
        $this->dropForeignKey('common_systemic_disorder_group_id_fk', 'common_systemic_disorder');
        $this->dropIndex('common_systemic_disorder_group_id_fk', 'common_systemic_disorder');
        $this->dropOEColumn('common_systemic_disorder', 'display_order', true);
        $this->dropOEColumn('common_systemic_disorder', 'group_id', true);
        $this->dropOETable('common_systemic_disorder_group', true);
    }
}
