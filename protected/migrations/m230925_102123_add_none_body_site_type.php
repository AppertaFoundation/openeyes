<?php

class m230925_102123_add_none_body_site_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('body_site_type', [
            'body_site_snomed_type' => 'None',
            'title_full' => 'None',
            'title_short' => 'None',
            'title_abbreviated' => 'None',
        ]);
    }

    public function safeDown()
    {
        $this->delete('body_site_type', 'body_site_snomed_type = ?', ['None']);
    }
}
