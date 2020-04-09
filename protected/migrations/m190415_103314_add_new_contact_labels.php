<?php

class m190415_103314_add_new_contact_labels extends CDbMigration
{
    public function up()
    {
        $this->insert('contact_label', ['name' => 'Parent', 'is_private' => 1]);
        $this->insert('contact_label', ['name' => 'Relative', 'is_private' => 1]);
        $this->insert('contact_label', ['name' => 'Next of Kin', 'is_private' => 1]);
        $this->insert('contact_label', ['name' => 'Carer', 'is_private' => 1]);
        $this->insert('contact_label', ['name' => 'Other', 'is_private' => 1]);
    }

    public function down()
    {
        $this->delete('contact_label', 'name = ?', ['Other']);
        $this->delete('contact_label', 'name = ?', ['Relative']);
        $this->delete('contact_label', 'name = ?', ['Next of Kin']);
        $this->delete('contact_label', 'name = ?', ['Carer']);
        $this->delete('contact_label', 'name = ?', ['Parent']);
    }
}
