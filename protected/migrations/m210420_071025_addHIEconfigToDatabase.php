<?php

class m210420_071025_addHIEconfigToDatabase extends OEMigration
{
    private $config = array(
        array(
            'element_type_id' => null,
            'field_type_id' => 'string',
            'key' => 'hie_remote_url',
            'name' => 'HIE API remote url',
            'default_value' => '',
        ),
        array(
            'element_type_id' => null,
            'field_type_id' => 'string',
            'key' => 'hie_usr_org',
            'name' => 'HIE API Organisation value (usr_org)',
            'default_value' => '',
        ),
        array(
            'element_type_id' => null,
            'field_type_id' => 'string',
            'key' => 'hie_usr_fac',
            'name' => 'HIE API FAC value (usr_fac)',
            'default_value' => '',
        ),
        array(
            'element_type_id' => null,
            'field_type_id' => 'string',
            'key' => 'hie_external',
            'name' => 'HIE API external value (hie_external)',
            'data' => 'both',
            'default_value' => 'both',
        ),
        array(
            'element_type_id' => null,
            'field_type_id' => 'string',
            'key' => 'hie_org_user',
            'name' => 'HIE API org user value (org_user)',
            'default_value' => '',
        ),
    );

    public function safeUp()
    {
        $text_field_id = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name="Text Field"')->queryScalar();
        $radio_field_id = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name="Radio buttons"')->queryScalar();

        foreach ($this->config as $item) {
            switch ($item['field_type_id']) {
                case 'radio':
                    $item['field_type_id'] = $radio_field_id;
                    break;
                case 'string':
                    $item['field_type_id'] = $text_field_id;
                    break;
                default:
                    throw new Exception("Invalid field_type: " . $item['field_type_id']);
                    break;
            }
            if (isset($item['data'])) {
                $item['data'] = is_array($item['data']) ? serialize($item['data']) : $item['data'];
            }

            $exists_meta_data = $this->dbConnection->createCommand()->select('id')->from('setting_metadata')->where('`key` = :setting_key', array(':setting_key' => $item['key']))->queryRow();
            if (!$exists_meta_data) {
                $this->insert('setting_metadata', $item);
            }
        }
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key` IN ("' . implode('","', array_column($this->config, 'key')) . '")');
        $this->delete('setting_metadata', '`key` IN ("' . implode('","', array_column($this->config, 'key')) . '")');
    }
}
