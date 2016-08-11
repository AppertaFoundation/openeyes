<?php

class m130916_143343_additional_mr_patient_shortcodes extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->registerShortcode($event_type, 'tcl', 'getLetterCentralSFTLeft', 'Central SFT for left eye from Examination');
        $this->registerShortcode($event_type, 'tcr', 'getLetterCentralSFTRight', 'Central SFT for right eye from Examination');
        $this->registerShortcode($event_type, 'tml', 'getLetterMaxCRTLeft', 'Maximim CRT for left eye from Examination');
        $this->registerShortcode($event_type, 'tmr', 'getLetterMaxCRTRight', 'Maximim CRT for right eye from Examination');
        $this->registerShortcode($event_type, 'iml', 'getLetterInjectionManagementComplexDiagnosisLeft', 'Injection Management Complex diagnosis for left eye from Examination');
        $this->registerShortcode($event_type, 'imr', 'getLetterInjectionManagementComplexDiagnosisRight', 'Injection Management Complex diagnosis for right eye from Examination');
    }

    public function registerShortcode($event_type, $code, $method, $description)
    {
        if (!preg_match('/^[a-zA-Z]{3}$/', $code)) {
            throw new Exception("Invalid shortcode: $code");
        }

        $default_code = $code;

        if ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => strtolower($code)))->queryRow()) {
            $n = '00';
            while ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => 'z'.$n))->queryRow()) {
                $n = str_pad((int) $n + 1, 2, '0', STR_PAD_LEFT);
            }
            $code = "z$n";

            echo "Warning: attempt to register duplicate shortcode '$default_code', replaced with 'z$n'\n";
        }

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type['id'],
            'code' => $code,
            'default_code' => $default_code,
            'method' => $method,
            'description' => $description,
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'tmr'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'tml'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'tcr'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'tcl'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'iml'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'imr'));
    }
}
