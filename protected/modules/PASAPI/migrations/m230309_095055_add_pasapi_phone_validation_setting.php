<?php

class m230309_095055_add_pasapi_phone_validation_setting extends OEMigration
{
    public function safeUp()
    {
        $this->addSetting('validate_PASAPI_phone_number', 'Validate phone number from PAS Request', 'When set to On, any phone numbers received in patient create/update messages for the PAS API will be checked to ensure that they have no non-numeric characters and that they strictly adhere to a [UK] phone number format. If the number in the message does not match this format, then the PAS message will be rejected
When set to off, this additional level of checking is disabled, and the phone number field will accept whatever text the PAS gives it. This is useful for some PAS systems that allow the phone number field to be abused for including additional comments like "only after 4pm", or allow brackets, hyphens, etc.', 'PASAPI', 'Checkbox', '', 1, 'INSTALLATION');
    }

    public function safeDown()
    {
        $this->deleteSetting('validate_PASAPI_phone_number');
    }
}
