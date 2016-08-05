<?php

class m130917_085323_additional_mr_patient_shortcodes extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphTrIntravitrealinjection'))->queryRow();

        $this->registerShortcode($event_type, 'idl', 'getLetterTreatmentDrugLeft', 'Intravitreal injection drug for left eye');
        $this->registerShortcode($event_type, 'idr', 'getLetterTreatmentDrugRight', 'Intravitreal injection drug for right eye');
        $this->registerShortcode($event_type, 'inl', 'getLetterTreatmentNumberLeft', 'Intravitreal injection number for left eye');
        $this->registerShortcode($event_type, 'inr', 'getLetterTreatmentNumberRight', 'Intravitreal injection number for right eye');
        $this->registerShortcode($event_type, 'pid', 'getLetterPostInjectionDrops', 'Post injection examination drops');
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
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'idl'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'idr'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'inl'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'inr'));
        $this->delete('patient_shortcode', 'code = :sc', array(':sc' => 'pid'));
    }
}
