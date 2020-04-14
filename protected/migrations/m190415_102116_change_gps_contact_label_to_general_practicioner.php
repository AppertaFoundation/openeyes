<?php

class m190415_102116_change_gps_contact_label_to_general_practicioner extends CDbMigration
{
    public function up()
    {
        Gp::$db = $this->dbConnection;
        $general_practicioner_label = $this->dbConnection->createCommand('SELECT id FROM contact_label WHERE name = :name')
            ->bindValues(array(':name' => 'General Practitioner'))
            ->queryScalar();
        if ($general_practicioner_label === null) {
            $this->insert('contact_label', array(
                'name' => 'General Practitioner',
            ));
        }
        $general_practicioners = $this->dbConnection->createCommand()
            ->select('c.id')
            ->from('gp')
            ->join('contact c', 'c.id = gp.contact_id')
            ->where('c.contact_label_id != :label_id OR c.contact_label_id IS NULL', array(':label_id' => $general_practicioner_label))
            ->queryAll();
        foreach ($general_practicioners as $general_practicioner) {
            $this->update(
                'contact',
                array('contact_label_id' => $general_practicioner_label),
                'id = :id',
                array(':id' => $general_practicioner['id'])
            );
        }
    }

    public function down()
    {
    }
}
