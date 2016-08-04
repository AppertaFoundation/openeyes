<?php

class m150506_104115_alterLensTypeTable extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('ophinbiometry_lenstype_lens_position_id_fk', 'ophinbiometry_lenstype_lens');
    }

    public function down()
    {
        $this->addForeignKey('ophinbiometry_lenstype_lens_position_id_fk', 'ophinbiometry_lenstype_lens', 'position_id', 'ophinbiometry_lens_position', 'id');
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
