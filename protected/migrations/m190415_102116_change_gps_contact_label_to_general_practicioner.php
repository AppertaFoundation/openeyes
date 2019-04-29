<?php

class m190415_102116_change_gps_contact_label_to_general_practicioner extends CDbMigration
{
    public function up()
    {
        $general_practicioner_label = ContactLabel::model()->find('name = ?', ["General Practitioner"]);
        if ($general_practicioner_label == null) {
            $general_practicioner_label = new ContactLabel();
            $general_practicioner_label->name = "General Practitioner";
            $general_practicioner_label->is_private = 0;
            $general_practicioner_label->save();
        }
        $general_practicioners = Gp::model()->with('contact')->findAll(
            'contact_label_id !=' . $general_practicioner_label->id . ' OR contact_label_id IS NULL'
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