<?php

class m201027_005751_add_statistic_triggers_to_ophgeneric_hfa_table extends OEMigration
{
    public function up()
    {
        $this->execute(
            <<<EOSQL
CREATE TRIGGER ins_stats	    
AFTER INSERT ON ophgeneric_hfa_entry
FOR EACH ROW  
BEGIN
    DECLARE l_exists, l_patient_id, l_patient_age, l_event_id INT;
    
    # Get the patient ID, DOB and date of death
    SELECT p.id, TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, e.event_date))
    INTO l_patient_id, l_patient_age
    FROM patient p
    JOIN episode ep ON ep.patient_id = p.id
    JOIN event e ON e.episode_id = ep.id
    JOIN et_ophgeneric_hfa hfa ON hfa.event_id = e.id
    WHERE hfa.id = NEW.element_id;
    
    # Determine if a statistic instance already exists. If not, create a new statistic instance
    SELECT COUNT(*)
    INTO l_exists
    FROM patient_statistic
    WHERE patient_id = l_patient_id
    AND stat_type_mnem = 'md'
    AND eye_id = NEW.eye_id;
    
    # Get the event ID to store as a back-reference in the statistics data model.
    SELECT hfa.event_id
    INTO l_event_id
    FROM et_ophgeneric_hfa hfa
    WHERE hfa.id = NEW.element_id;
    
    IF l_exists = 0 THEN
        INSERT INTO patient_statistic (
            patient_id,
            stat_type_mnem,
            eye_id
        ) VALUES (
            l_patient_id,
            'md',
            NEW.eye_id
        );
    END IF;
    
    SELECT COUNT(*)
    INTO l_exists
    FROM patient_statistic
    WHERE patient_id = l_patient_id
    AND stat_type_mnem = 'vfi'
    AND eye_id = NEW.eye_id;
    
    IF l_exists = 0 THEN
        INSERT INTO patient_statistic (
            patient_id,
            stat_type_mnem,
            eye_id
        ) VALUES (
            l_patient_id,
            'vfi',
            NEW.eye_id
        );
    END IF;
    
    INSERT INTO patient_statistic_datapoint (
        patient_id,
        stat_type_mnem,
        eye_id,
        x_value,
        y_value,
        event_id
    ) VALUES (
        l_patient_id,
        'md',
        NEW.eye_id,
        l_patient_age,
        NEW.mean_deviation,
        l_event_id
    );
    
    INSERT INTO patient_statistic_datapoint (
        patient_id,
        stat_type_mnem,
        eye_id,
        x_value,
        y_value,
        event_id
    ) VALUES (
        l_patient_id,
        'vfi',
        NEW.eye_id,
        l_patient_age,
        NEW.visual_field_index,
        l_event_id
    );
    
    UPDATE patient_statistic SET
        process_datapoints = 1
    WHERE patient_id = l_patient_id
    AND stat_type_mnem IN ('md', 'vfi')
    AND eye_id = NEW.eye_id;
END;
EOSQL
        );

        $this->execute(
            <<<EOSQL
CREATE TRIGGER update_stats
AFTER UPDATE ON ophgeneric_hfa_entry
FOR EACH ROW
BEGIN
    DECLARE l_patient_id, l_patient_age INT;
    
    # Get the patient ID, DOB and date of death
    SELECT p.id, TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, e.event_date))
    INTO l_patient_id, l_patient_age
    FROM patient p
    JOIN episode ep ON ep.patient_id = p.id
    JOIN event e ON e.episode_id = ep.id
    JOIN et_ophgeneric_hfa hfa ON hfa.event_id = e.id
    WHERE hfa.id = NEW.element_id;
    
    INSERT INTO patient_statistic_datapoint_version (
        id,
        patient_id,
        stat_type_mnem,
        eye_id,
        x_value,
        y_value,
        event_id,
        last_modified_user_id,
        last_modified_date,
        created_user_id,
        created_date,
        version_date
    )
    SELECT psd.*, CURDATE()
    FROM patient_statistic_datapoint psd
    WHERE patient_id = l_patient_id
    AND stat_type_mnem IN ('md', 'vfi')
    AND eye_id = NEW.eye_id;
    
    UPDATE patient_statistic_datapoint SET
        x_value = l_patient_age,
        y_value = NEW.mean_deviation
    WHERE patient_id = l_patient_id
    AND stat_type_mnem = 'md'
    AND eye_id = NEW.eye_id;
    
    UPDATE patient_statistic_datapoint SET
        x_value = l_patient_age,
        y_value = NEW.visual_field_index
    WHERE patient_id = l_patient_id
    AND stat_type_mnem = 'vfi'
    AND eye_id = NEW.eye_id;
    
    # No need to update versioning table here as the reprocessing script will handle this.
    UPDATE patient_statistic SET
        process_datapoints = 1
    WHERE patient_id = l_patient_id
    AND stat_type_mnem IN ('md', 'vfi')
    AND eye_id = NEW.eye_id;
END;
EOSQL
        );
    }

    public function down()
    {
        $this->execute('DROP TRIGGER ins_stats');
        $this->execute('DROP TRIGGER update_stats');
    }
}
