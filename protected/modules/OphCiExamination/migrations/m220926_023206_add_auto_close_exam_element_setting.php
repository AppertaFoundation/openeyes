<?php

class m220926_023206_add_auto_close_exam_element_setting extends OEMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'display_order' => 0,
            'field_type_id' => 3,
            'key' => 'close_incomplete_exam_elements',
            'name' => 'Offer to automatically close incomplete examination elements',
            'lowest_setting_level' => 'INSTITUTION',
            'data' => serialize(array('on' => 'On', 'off' => 'Off'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'close_incomplete_exam_elements',
            'value' => 'off'
        ));

        $institutions = $this->dbConnection->createCommand()
            ->select('id AS institution_id')
            ->from('institution')
            ->queryColumn();

        $institutions = array_map(
            static function ($item) {
                return [
                    'institution_id' => $item,
                    'key' => 'close_incomplete_exam_elements',
                    'value' => 'off',
                ];
            },
            $institutions
        );

        $this->insertMultiple('setting_institution', $institutions);
    }

    public function safeDown()
    {
        $this->delete('setting_institution', '`key` = "close_incomplete_exam_elements"');
        $this->delete('setting_installation', '`key` = "close_incomplete_exam_elements"');
        $this->delete('setting_metadata', '`key` = "close_incomplete_exam_elements"');
    }
}
