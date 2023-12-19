<?php

class m231115_172620_update_clinic_outcome_risk_status extends OEMigration
{
    private array $replace = [
        'Irreversible hard from delayed appointment. Do NOT reschedule patient.' => 'Irreversible harm from delayed appointment. Do NOT reschedule patient.',
        'Reversible hard from delayed appointment.' => 'Reversible harm from delayed appointment.'
    ];

    public function up()
    {
        foreach ($this->replace as $from => $to) {
            $this->update(
                'ophciexamination_clinicoutcome_risk_status',
                ['description' => $to],
                "description = :from",
                [':from' => $from]
            );
        }
    }

    public function down()
    {
        foreach ($this->replace as $from => $to) {
            $this->update(
                'ophciexamination_clinicoutcome_risk_status',
                ['description' => $from],
                "description = :to",
                [':to' => $to]
            );
        }
    }
}
