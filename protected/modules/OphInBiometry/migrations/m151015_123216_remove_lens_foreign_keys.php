<?php

class m151015_123216_remove_lens_foreign_keys extends CDbMigration
{
    /*
     *
     */
    public function up()
    {
        //Removing the foreign key for saving data without any lense.
        $this->dropForeignKey('ophinbiometry_lenstype_lens_l_fk', 'et_ophinbiometry_selection');
        $this->dropForeignKey('ophinbiometry_lenstype_lens_r_fk', 'et_ophinbiometry_selection');

        $this->delete('ophinbiometry_lenstype_lens', "name = 'unknown'");
    }

    public function down()
    {

        //Adding a new row in ophinbiometry_lenstype_lens, so that we can keep the data foreign key constrains.
        $lensType = $this->dbConnection->createCommand()->select('*')->from('ophinbiometry_lenstype_lens')->where("name = 'unknown'")->queryRow();

        if ($lensType == null) {
            $this->insert('ophinbiometry_lenstype_lens', array('name' => 'unknown', 'deleted' => 0, 'description' => 'system default value', 'position_id' => 1, 'comments' => ''));
            $lensType = $this->dbConnection->createCommand()->select('*')->from('ophinbiometry_lenstype_lens')->where("name = 'unknown'")->queryRow();
        }

        $this->update('et_ophinbiometry_selection', array('lens_id_left' => $lensType['id']), 'lens_id_left = 0');
        $this->update('et_ophinbiometry_selection', array('lens_id_right' => $lensType['id']), 'lens_id_right = 0');

        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_l_fk',
            'et_ophinbiometry_selection',
            'lens_id_left',
            'ophinbiometry_lenstype_lens',
            'id'
        );
        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_r_fk',
            'et_ophinbiometry_selection',
            'lens_id_right',
            'ophinbiometry_lenstype_lens',
            'id'
        );
    }
}
