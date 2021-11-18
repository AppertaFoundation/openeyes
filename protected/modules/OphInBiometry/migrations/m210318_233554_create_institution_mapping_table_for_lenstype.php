<?php

class m210318_233554_create_institution_mapping_table_for_lenstype extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophinbiometry_lenstype_lens_institution',
            array(
                'id' => 'pk',
                'lenstype_lens_id' => 'int(10) unsigned NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );

        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_institution_i_fk',
            'ophinbiometry_lenstype_lens_institution',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_institution_l_fk',
            'ophinbiometry_lenstype_lens_institution',
            'lenstype_lens_id',
            'ophinbiometry_lenstype_lens',
            'id'
        );

        $this->dbConnection->createCommand("INSERT INTO ophinbiometry_lenstype_lens_institution (lenstype_lens_id, institution_id)
                                            SELECT id, (SELECT id FROM institution WHERE `remote_id`=:remote_id)
                                            FROM ophinbiometry_lenstype_lens oll ")
                                            ->execute(array('remote_id' => Yii::app()->params['institution_code']));
    }

    public function down()
    {
        $this->dropForeignKey(
            'ophinbiometry_lenstype_lens_institution_i_fk',
            'ophinbiometry_lenstype_lens_institution',
        );

        $this->dropForeignKey(
            'ophinbiometry_lenstype_lens_institution_l_fk',
            'ophinbiometry_lenstype_lens_institution',
        );

        $this->dropOETable(
            'ophinbiometry_lenstype_lens_institution',
            true
        );
    }
}
