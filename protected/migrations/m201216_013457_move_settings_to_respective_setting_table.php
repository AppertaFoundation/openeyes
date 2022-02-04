<?php

class m201216_013457_move_settings_to_respective_setting_table extends OEMigration
{
    private array $institutionLevelSettingKeys = [
        'html_autocomplete',
        'watermark',
        'watermark_admin',
        'alerts_email',
        'adminEmail',
        'default_incision_length',
        'applications_alert_recipients',
        'optom_comment_alert',
        'correspondence_create_banner',
        'enable_prescriptions_edit',
        'mandatory_post_op_instructions',
        'require_exam_before_booking',
        'ask_correspondence_approval',
        'vte_assessment_element_enabled',
        'disable_theatre_diary',
        'context_firm_label',
        'enable_concise_med_history',
        'OphCoTherapyapplication_sender_email',
        'disable_print_notes_copy',
        'opnote_lens_migration_link',
        'disable_prescription_patient_copy',
        'disable_auto_feature_tours',
        'prescription_boilerplate_header',
        'prescription_boilerplate_footer',
        'display_theme',
        'nhs_num_label',
        'element_close_warning_enabled',
        'op_booking_disable_golden_patient',
        'pre_assessment_booking_default_value',
        'watermark_short',
        'watermark_admin_short',
        'set_auto_increment_hospital_no',
        'hos_num_start',
        'gp_label',
        'general_practitioner_label',
        'user_add_disorder',
        'include_subspecialty_name_in_unbooked_worklists',
        'worklist_past_search_days',
        'worklist_future_search_days',
        'disable_auto_import_optoms_from_portal',
        'op_booking_inc_time_high_complexity',
        'op_booking_decrease_time_low_complexity',
        'default_post_op_drug_set',
        'default_post_op_letter',
        'default_optom_post_op_letter',
        'auto_generate_prescription_after_surgery',
        'auto_generate_gp_letter_after_surgery',
        'auto_generate_optom_post_op_letter_after_surgery',
        'imagenet_url',
        'prescription_form_format',
        'default_prescription_code_code',
        'fp10_department_name',
        'fp10_institution_name',
        'nhs_num_label_short',
        'hos_num_label_short',
        'opnote_whiteboard_display_mode',
        'theatre_diary_whiteboard_display_mode',
        'patient_phone_number_mandatory',
        'default_patient_source',
        'hos_num_label',
        'training_mode_enabled',
        'cataract_eur_switch',
        'hos_num_label'
    ];

    public function safeUp()
    {
        $this->addOEColumn('setting_metadata', 'lowest_setting_level', 'varchar(15) DEFAULT \'INSTALLATION\' AFTER default_value', true);

        // update lowest_setting_level to INSTITUTION
        $keys = "'" . implode("', '", $this->institutionLevelSettingKeys) . "'";
        $updateQuery = "UPDATE setting_metadata as sm SET sm.lowest_setting_level = 'INSTITUTION' WHERE sm.`key` IN ($keys);";
        $this->dbConnection->createCommand($updateQuery)->execute();

        // Move the existing institution level settings from the setting_installation to setting_institution table.
        $settingInstallations = $this->dbConnection->createCommand("SELECT * FROM setting_installation si WHERE si.`key` IN ($keys)")->queryAll();
        $institution_id = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")->queryScalar();

        foreach ($settingInstallations as $settingInstallation) {
            unset($settingInstallation['id']);
            $settingInstallation['institution_id'] = $institution_id;
            $this->insert('setting_institution', $settingInstallation);
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn('setting_metadata', 'lowest_setting_level', true);
    }
}
