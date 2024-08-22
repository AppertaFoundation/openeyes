<?php

class m210419_150800_create_cito_settings_data extends OEMigration
{
    private $keys = [
        'cito_access_token_url' => 'Civica Cito API access token URL',
        'cito_otp_url' => 'Civica Cito API OTP URL',
        'cito_sign_url' => 'Civica Cito API sign URL',
        'cito_grant_type' => 'Civica Cito API grant type',
        'cito_client_id' => 'Civica Cito API client id',
    ];
    public function safeUp()
    {
        foreach ($this->keys as $key => $value) {
            $exists_meta_data = $this->dbConnection->createCommand()->select('id')->from('setting_metadata')->where('`key` = :setting_key', array(':setting_key' => $key))->queryScalar();

            if (!$exists_meta_data) {
                $this->insert('setting_metadata', array(
                    'element_type_id' => null,
                    'display_order' => 22,
                    'field_type_id' => 4,
                    'key' => $key,
                    'name' => $value,
                    'data' => '',
                    'default_value' => ''
                ));
                $this->insert('setting_installation', [
                    'key' => $key,
                ]);
            }
        }
    }

    public function safeDown()
    {
        $keys =  "'" . implode("','", array_keys($this->keys)) . "'";
        $this->delete('setting_metadata', '`key` IN (' . $keys . ')');
        $this->delete('setting_installation', '`key` IN (' . $keys . ')');
    }
}
