<?php

class m131023_100530_both_eye_shortcodes extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCoTherapyapplication'))->queryRow();

        $this->registerShortcode($event_type, 'tdb', 'getLetterApplicationDiagnosisBoth', 'Therapy application diagnosis for both eyes');
        $this->registerShortcode($event_type, 'ttb', 'getLetterApplicationTreatmentBoth', 'Therapy application treatment for both eyes');
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'method = :meth', array(':meth' => 'getLetterApplicationTreatmentBoth'));
        $this->delete('patient_shortcode', 'method = :meth', array(':meth' => 'getLetterApplicationDiagnosisBoth'));
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
