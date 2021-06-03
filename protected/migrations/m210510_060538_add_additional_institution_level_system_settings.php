<?php

class m210510_060538_add_additional_institution_level_system_settings extends OEMigration
{
    private array $institutionLevelSettingKeys = array(
        'send_email_immediately',
        'send_email_delayed',
        'letter_header',
        'enable_forum_integration',
        'OphCiExamination_default_iris_colour',
        'disable_manual_biometry',
        'display_institution_name',
        'enable_patient_import',
        'enable_prescription_overprint',
        'correspondence_delayed_email_processing',
        'manually_add_emails_correspondence',
    );

    /**
     * @return void
     * @throws CDbException
     * @throws CException
     */
    public function safeUp()
    {
        // update lowest_setting_level to INSTITUTION
        $keys = "'" . implode("', '", $this->institutionLevelSettingKeys) . "'";
        $this->update(
            'setting_metadata',
            array('lowest_setting_level' => 'INSTITUTION'),
            "`key` IN ($keys)"
        );

        // Move the existing institution level settings from the setting_installation to setting_institution table.
        $settingInstallations = $this->dbConnection->createCommand(
            "SELECT * FROM setting_installation WHERE `key` IN ($keys)"
        )->queryAll();
        $institution_id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->from('institution')
            ->where('remote_id = :institution_code')
            ->bindValues([':institution_code' => Yii::app()->params['institution_code']])
            ->queryScalar();

        foreach ($settingInstallations as $settingInstallation) {
            unset($settingInstallation['id']);
            $settingInstallation['institution_id'] = $institution_id;
            $this->insert('setting_institution', $settingInstallation);
        }
    }

    /**
     * @return void
     * @throws CDbException
     * @throws CException
     */
    public function safeDown()
    {
        $keys = "'" . implode("', '", $this->institutionLevelSettingKeys) . "'";
        $this->update(
            'setting_metadata',
            array('lowest_setting_level' => 'INSTALLATION'),
            "`key` IN ($keys)"
        );

        // Move the existing institution level settings from the setting_institution to setting_installation table.
        $institution_id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->from('institution')
            ->where('remote_id = :institution_code')
            ->bindValues([':institution_code' => Yii::app()->params['institution_code']])
            ->queryScalar();

        // As the existing setting_installation values haven't been deleted, just remove the institution-level overrides.
        $this->delete(
            "setting_institution",
            "`key` IN ($keys) AND si.institution_id = :institution_id",
            ['institution_id' => $institution_id]
        );
    }
}
