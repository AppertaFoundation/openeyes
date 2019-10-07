<?php

class m191007_074926_update_medication_usage_codes extends CDbMigration
{
	public function up()
  {
    $this->update('medication_usage_code', ['name' => 'Common Ophthalmic'], 'usage_code = :usage_code', [':usage_code' => "COMMON_OPH"]);
    $this->update('medication_usage_code', ['name' => 'Common Systemic'], 'usage_code = :usage_code', [':usage_code' => "COMMON_SYSTEMIC"]);
    $this->update('medication_usage_code', ['name' => 'Prescription'], 'usage_code = :usage_code', [':usage_code' => "PRESCRIPTION_SET"]);
    $this->update('medication_usage_code', ['name' => 'Formulary'], 'usage_code = :usage_code', [':usage_code' => "Formulary"]);
    $this->update('medication_usage_code', ['active' => 0, 'hidden' => 1], 'usage_code = :usage_code', [':usage_code' => "DrugTag"]);
    $this->update('medication_usage_code', ['active' => 0, 'hidden' => 1], 'usage_code = :usage_code', [':usage_code' => "MedicationDrug"]);
    $this->update('medication_usage_code', ['active' => 0, 'hidden' => 1], 'usage_code = :usage_code', [':usage_code' => "Management"]);
	}

	public function down()
	{
    $this->update('medication_usage_code', ['name' => 'Common Ophthalmic Drug Sets'], 'usage_code = :usage_code', [':usage_code' => "COMMON_OPH"]);
    $this->update('medication_usage_code', ['name' => 'Common Systemic Drug Sets'], 'usage_code = :usage_code', [':usage_code' => "COMMON_SYSTEMIC"]);
    $this->update('medication_usage_code', ['name' => 'Prescription Drug Sets'], 'usage_code = :usage_code', [':usage_code' => "PRESCRIPTION_SET"]);
    $this->update('medication_usage_code', ['name' => 'Formulary Drugs'], 'usage_code = :usage_code', [':usage_code' => "Formulary"]);
    $this->update('medication_usage_code', ['active' => 1, 'hidden' => 0], 'usage_code = :usage_code', [':usage_code' => "DrugTag"]);
    $this->update('medication_usage_code', ['active' => 1, 'hidden' => 0], 'usage_code = :usage_code', [':usage_code' => "MedicationDrug"]);
    $this->update('medication_usage_code', ['active' => 1, 'hidden' => 0], 'usage_code = :usage_code', [':usage_code' => "Management"]);
	}
}
