<?php

class m161021_110716_add_eye_comments extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophinbiometry_calculation', 'comments_right', 'text');
        $this->addColumn('et_ophinbiometry_calculation', 'comments_left', 'text');
        $this->addColumn('et_ophinbiometry_calculation_version', 'comments_right', 'text');
        $this->addColumn('et_ophinbiometry_calculation_version', 'comments_left', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_calculation', 'comments_right');
        $this->dropColumn('et_ophinbiometry_calculation', 'comments_left');
        $this->dropColumn('et_ophinbiometry_calculation_version', 'comments_right');
        $this->dropColumn('et_ophinbiometry_calculation_version', 'comments_left');
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
