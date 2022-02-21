<?php

class m210413_052527_add_created_institution_id_column_to_contact_table extends OEMigration
{
    public function safeUp()
    {
        // Contacts will be accessible by any institution, but only the creating institution can edit or delete them.
        $this->addOEColumn('contact', 'created_institution_id', 'int(11) unsigned', true);
        $this->addForeignKey(
            'contact_created_institution_fk',
            'contact',
            'created_institution_id',
            'institution',
            'id'
        );

        $institution_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->where('remote_id = :code')
            ->bindValue(':code', Yii::app()->params['institution_code'])
            ->queryScalar();

        $this->update(
            'contact',
            array('created_institution_id' => $institution_id)
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'contact_created_institution_fk',
            'contact',
        );
        $this->dropOEColumn('contact', 'created_institution_id', true);
    }
}
