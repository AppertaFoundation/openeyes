<?php

class m221003_002558_admin_setting_auto_add_book_apt_step extends CDbMigration
{
    public function safeUp()
    {
        $field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where(
                'name = :name',
                array(':name' => 'Radio buttons'),
            )->queryScalar();

        $this->insert('setting_metadata', array(
            'key' => 'auto_add_book_apt_step',
            'name' => 'Auto add Book Apt. Step from Examination->Follow-up',
            'data' => serialize(array('on' => 'On', 'off' => 'Off')),
            'field_type_id' => $field_type_id,
            'default_value' => 'on',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "auto_add_book_apt_step"');
    }
}
