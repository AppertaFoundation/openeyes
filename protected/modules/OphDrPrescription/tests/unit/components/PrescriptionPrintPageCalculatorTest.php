<?php

/**
 * Class PrescriptionPrintPageCalculatorTest
 */
class PrescriptionPrintPageCalculatorTest extends OEDbTestCase
{
    use FakesSettingMetadata;
    /** @test */
    public function returns_one_when_both_settings_off()
    {
        $this->settingSetup(SettingMetadata::$OFF_SETTING_VALUE, SettingMetadata::$OFF_SETTING_VALUE);
        $expected = 3;

        $actual = PrescriptionPrintPageCountCalculator::calculatePageCountWithSettings();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function returns_two_when_print_notes_on_and_patient_copy_off()
    {
        $this->settingSetup(SettingMetadata::$ON_SETTING_VALUE, SettingMetadata::$OFF_SETTING_VALUE);
        $expected = 2;

        $actual = PrescriptionPrintPageCountCalculator::calculatePageCountWithSettings();

        $this->assertEquals($expected, $actual);
    }
    /** @test */
    public function returns_two_when_print_notes_off_and_patient_copy_on()
    {

        $this->settingSetup(SettingMetadata::$OFF_SETTING_VALUE, SettingMetadata::$ON_SETTING_VALUE);
        $expected = 2;

        $actual = PrescriptionPrintPageCountCalculator::calculatePageCountWithSettings();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function returns_three_when_both_settings_on()
    {
        $this->settingSetup(SettingMetadata::$ON_SETTING_VALUE, SettingMetadata::$ON_SETTING_VALUE);
        $expected = 1;

        $actual = PrescriptionPrintPageCountCalculator::calculatePageCountWithSettings();

        $this->assertEquals($expected, $actual);
    }

    private function settingSetup($disable_print_notes_copy_value, $disable_prescription_patient_copy_value)
    {
        $this->fakeSettingMetadata('disable_print_notes_copy', $disable_print_notes_copy_value);
        $this->fakeSettingMetadata('disable_prescription_patient_copy', $disable_prescription_patient_copy_value);
    }
}
