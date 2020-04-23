<?php

class m160831_100814_demographics_tweaks extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophcocvi_demographics', 'gender');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gender');
        $this->dropColumn('et_ophcocvi_demographics', 'name');
        $this->dropColumn('et_ophcocvi_demographics_version', 'name');
        $this->addColumn('et_ophcocvi_demographics', 'title_surname', 'varchar(120)');
        $this->addColumn('et_ophcocvi_demographics_version', 'title_surname', 'varchar(120)');
        $this->addColumn('et_ophcocvi_demographics', 'other_names', 'varchar(100)');
        $this->addColumn('et_ophcocvi_demographics_version', 'other_names', 'varchar(100)');
        $this->addColumn('et_ophcocvi_demographics', 'gender_id', 'int(10) unsigned');
        $this->addColumn('et_ophcocvi_demographics_version', 'gender_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_demographics_gui_fk', 'et_ophcocvi_demographics', 'gender_id', 'gender', 'id');
        $this->addColumn('et_ophcocvi_demographics', 'ethnic_group_id', 'int(10) unsigned');
        $this->addColumn('et_ophcocvi_demographics_version', 'ethnic_group_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_demographics_egui_fk', 'et_ophcocvi_demographics', 'ethnic_group_id', 'ethnic_group', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_demographics_egui_fk', 'et_ophcocvi_demographics');
        $this->dropForeignKey('et_ophcocvi_demographics_gui_fk', 'et_ophcocvi_demographics');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gender_id');
        $this->dropColumn('et_ophcocvi_demographics', 'gender_id');
        $this->dropColumn('et_ophcocvi_demographics_version', 'ethnic_group_id');
        $this->dropColumn('et_ophcocvi_demographics', 'ethnic_group_id');
        $this->dropColumn('et_ophcocvi_demographics_version', 'title_surname');
        $this->dropColumn('et_ophcocvi_demographics', 'title_surname');
        $this->dropColumn('et_ophcocvi_demographics_version', 'other_names');
        $this->dropColumn('et_ophcocvi_demographics', 'other_names');

        $this->addColumn('et_ophcocvi_demographics', 'gender', 'varchar(20)');
        $this->addColumn('et_ophcocvi_demographics_version', 'gender', 'varchar(20)');
        $this->addColumn('et_ophcocvi_demographics', 'name', 'varchar(255)');
        $this->addColumn('et_ophcocvi_demographics_version', 'name', 'varchar(255)');
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
