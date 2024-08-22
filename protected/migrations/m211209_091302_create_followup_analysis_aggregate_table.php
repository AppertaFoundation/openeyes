<?php

class m211209_091302_create_followup_analysis_aggregate_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'followup_analysis_aggregate', [
                'id' => 'pk',
                'patient_id' => 'int(10) unsigned NOT NULL',
                'event_id' => 'int(10) unsigned DEFAULT NULL', // Not necessarily used by tickets
                'ticket_id' => 'int(11) DEFAULT NULL',
                'type' => 'varchar(16) NOT NULL',
                'made_at_date' => 'datetime NOT NULL',
                'due_date' => 'datetime DEFAULT NULL' // Not used by referral letters
            ],
            false);

        $this->addForeignKey(
            'followup_analysis_aggregate_patient_fk',
            'followup_analysis_aggregate',
            'patient_id',
            'patient',
            'id'
        );

        $this->importFollowUpExaminationsEvents();
        $this->importPatientTicketingFollowUps();
        $this->importReferralLetterEvents();

        // Remove out of date entries
        $this->execute(
            'DELETE FROM followup_analysis_aggregate ' .
            'WHERE ABS(CAST(DATEDIFF(due_date, current_date()) / 7 AS INT)) >' . \FollowupAnalysisAggregate::FOLLOWUP_WEEK_LIMITED
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'followup_analysis_aggregate_patient_fk',
            'followup_analysis_aggregate'
        );

        $this->dropOETable('followup_analysis_aggregate', false);
    }

    private function importFollowUpExaminationsEvents()
    {
        $this->execute(
            'INSERT INTO followup_analysis_aggregate (patient_id, event_id, type, made_at_date, due_date) ' .
            'SELECT p.id, e.id, "' . \FollowupAnalysisAggregate::TYPE_FOLLOWUP . '", e.event_date, ' .
            '  DATE_ADD(e.event_date, INTERVAL IF(period.name = "weeks", 7 ,IF( period.name = "months", 30, IF(period.name = "years", 365, 1)))*eoc_entry.followup_quantity DAY) ' .
            'FROM event e ' .
            'LEFT JOIN episode e2 ON e.episode_id = e2.id ' .
            'LEFT JOIN patient p ON p.id = e2.patient_id ' .
            'LEFT JOIN event_type e3 ON e3.id = e.event_type_id ' .
            'LEFT JOIN et_ophciexamination_clinicoutcome eoc ON eoc.event_id = e.id ' .
            'LEFT JOIN ophciexamination_clinicoutcome_entry eoc_entry ON eoc_entry.element_id = eoc.id ' .
            'LEFT JOIN period ON period.id = eoc_entry.followup_period_id ' .
            'WHERE p.deleted <> 1 AND e.deleted <> 1 AND e2.deleted <> 1 ' .
            '  AND lower(e3.name) LIKE lower("%examination%") AND eoc.id IS NOT NULL ' .
            '  AND eoc_entry.followup_period_id IS NOT NULL AND e.event_date = (' .
            '    SELECT MAX(e4.event_date) FROM event e4 ' .
            '    LEFT JOIN episode e5 ON e4.episode_id = e5.id ' .
            '    LEFT JOIN patient p2 ON e5.patient_id = p2.id ' .
            '    LEFT JOIN event_type e6 ON e6.id = e4.event_type_id ' .
            '    WHERE p2.id = p.id AND e4.deleted = 0 AND e5.deleted = 0 ' .
            '      AND lower(e3.name) LIKE lower("%examination%")' .
            ')'
        );
    }

    private function importPatientTicketingFollowUps()
    {
        $this->execute(
            'INSERT INTO followup_analysis_aggregate (patient_id, event_id, ticket_id, type, made_at_date, due_date) ' .
            'SELECT ptt.patient_id, ptt.event_id, ptt.id, "' . \FollowupAnalysisAggregate::TYPE_TICKETED . '", pta.assignment_date, ' .
            '  DATE_ADD(pta.assignment_date, INTERVAL IF(period.name = "weeks", 7 ,IF( period.name = "months", 30, IF(period.name = "years", 365, 1)))*JSON_VALUE(pta.details, "$[0].value.followup_quantity") DAY) ' .
            'FROM patientticketing_ticket ptt ' .
            'JOIN event e ON e.id = ptt.event_id ' .
            'LEFT JOIN event_type et ON et.id = e.event_type_id ' .
            'JOIN patientticketing_ticketqueue_assignment pta ON pta.ticket_id = ptt.id ' .
            'JOIN patientticketing_ticketassignoutcomeoption pto ON pto.id = JSON_VALUE(pta.details, "$[0].value.outcome")' .
            'JOIN period ON period.name = JSON_VALUE(pta.details, "$[0].value.followup_period")' .
            'WHERE LOWER(et.name) = "examination" AND pto.followup = 1'
        );
    }

    private function importReferralLetterEvents()
    {
        $this->execute(
            'INSERT INTO followup_analysis_aggregate (patient_id, event_id, type, made_at_date) ' .
            'SELECT p.id, e.id, "' . \FollowupAnalysisAggregate::TYPE_REFERRAL . '", e.event_date ' .
            'FROM event e ' .
            'LEFT JOIN episode e2 ON e.episode_id = e2.id ' .
            'LEFT JOIN patient p ON p.id = e2.patient_id ' .
            'LEFT JOIN event_type e3 ON e3.id = e.event_type_id ' .
            'LEFT JOIN et_ophcodocument_document eod ON e.id = eod.event_id ' .
            'LEFT JOIN ophcodocument_sub_types ost ON eod.event_sub_type = ost.id ' .
            'WHERE ost.name = "Referral Letter" AND p.deleted <> 1 AND e.deleted <> 1 AND e2.deleted <> 1 ' .
            '  AND lower(e3.name) LIKE lower("%document%") AND e.event_date = (' .
            '    SELECT MAX(e4.event_date) FROM event e4 ' .
            '    LEFT JOIN episode e5 ON e4.episode_id = e5.id ' .
            '    LEFT JOIN patient p2 ON e5.patient_id = p2.id ' .
            '    LEFT JOIN event_type e6 ON e6.id = e4.event_type_id ' .
            '    WHERE p2.id = p.id AND e4.deleted = 0 AND e5.deleted = 0 ' .
            '      AND lower(e3.name) LIKE lower("%document%")' .
            ')'
        );
    }
}
