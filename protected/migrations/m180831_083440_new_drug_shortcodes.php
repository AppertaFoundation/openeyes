<?php

class m180831_083440_new_drug_shortcodes extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->registerShortcode(
            $event_type,
            'dst',
            'getLetterDrugsStartedToday',
            'Drugs started today'
        );

        $this->registerShortcode(
            $event_type,
            'dsp',
            'getLetterDrugsStoppedToday',
            'Drugs stopped today'
        );

        $this->registerShortcode(
            $event_type,
            'dct',
            'getLetterDrugsContinuedToday',
            'Drugs continued today'
        );
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'code = :code', array(':code' => 'dst'));
        $this->delete('patient_shortcode', 'code = :code', array(':code' => 'dsp'));
        $this->delete('patient_shortcode', 'code = :code', array(':code' => 'dct'));
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
