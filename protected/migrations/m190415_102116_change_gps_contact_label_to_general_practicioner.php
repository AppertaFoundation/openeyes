<?php

class m190415_102116_change_gps_contact_label_to_general_practicioner extends CDbMigration
{
    public function up()
    {
        $general_practicioner_label = ContactLabel::model()->find('name = ?', ["General Practitioner"]);
        $general_practicioners = Gp::model()->with('contact')->findAll(
            'contact_label_id !=' . $general_practicioner_label->id
        );
        foreach ($general_practicioners as $general_practicioner) {
            $general_practicioner->contact->contact_label_id = $general_practicioner_label->id;
            $general_practicioner->contact->save();
        }
    }

    public function down()
    {
    }
}