<?php

class m131022_135806_missing_mgmt_findings_shortcodes extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->registerShortcode($event_type, 'lmf', 'getLetterLaserManagementFindings', 'Laser management findings from latest examination');
        $this->registerShortcode($event_type, 'imf', 'getLetterInjectionManagementComplexFindings', 'Injection management findings from latest examination');
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'method = :meth', array(':meth' => 'getLetterInjectionManagementComplexFindings'));
        $this->delete('patient_shortcode', 'method = :meth', array(':meth' => 'getLetterLaserManagementFindings'));
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
}
