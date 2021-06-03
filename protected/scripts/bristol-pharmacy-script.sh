#!/bin/bash -l

read -r -p "Enter username of the mysql user: " USERNAME
read -r -p "Enter password of the mysql user: " PASSWORD

## default DB connection variables
# If database user / pass are empty then set from environment variables of from docker secrets (secrets are the recommended approach)
# Note that this script ignores the old db.conf method. If you are still using this deprecated
# method, then you'll need to manually set the relevant environment variables to match your db.conf
if [ -n "$MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$MYSQL_ROOT_PASSWORD"
elif [ -f "/run/secrets/MYSQL_ROOT_PASSWORD" ]; then
    dbpassword="$(</run/secrets/MYSQL_ROOT_PASSWORD)"
else
    dbpassword=""
fi

# add -p to front of dbpassword (deals with blank dbpassword)
if [ -n "$dbpassword" ]; then
    dbpassword="-p'$dbpassword'"
fi

if [ -n "$MYSQL_SUPER_USER" ]; then
    username="$MYSQL_SUPER_USER"
elif [ -f "/run/secrets/MYSQL_SUPER_USER" ]; then
    username="$(</run/secrets/MYSQL_SUPER_USER)"
else
    # fallback to using root for deleting and restoring DB
    username="root"
fi

dbschema=${DATABASE_NAME:-"openeyes"}
port=${DATABASE_PORT:-"3306"}
host=${DATABASE_HOST:-"localhost"}

dbconnectionstring="mysql -u '$username' $dbpassword --port=$port --host=$host";

eval "$dbconnectionstring -D $dbschema" << EOF
/*
 * MySQL script for the Bristol Pharmacy - create-views-and-read-only-dbuser-for-bristol-pharmacy.sql.
 * You need to run this script with an authorized user.
 * This script creates v_prescriptions, v_prescription_items and v_prescription_item_tapers views and also, creates a user with a read only access to these views.
 */
USE openeyes;

START TRANSACTION;

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW v_prescriptions AS
SELECT
    p.id AS patient_id,
    p.hos_num AS 'patient_hospital_number',
    p.nhs_num AS 'patient_nhs_number',
    e.id AS 'event_id',
    emu2.dispense_condition_id,
    odc.name AS 'dispense_condition_name',
    emu2.dispense_location_id,
    odl.name AS 'dispense_location_name',
    eod.comments,
    eod.draft,
    COALESCE(oer.caption, eod.edit_reason_other) AS 'edit_reason'
FROM et_ophdrprescription_details eod
    JOIN event e
        ON e.id = eod.event_id
    JOIN episode ep
        ON ep.id = e.episode_id
    JOIN patient p
        ON p.id = ep.patient_id
    JOIN (
        SELECT emu.event_id, emu.dispense_condition_id, emu.dispense_location_id, emu.id, ROW_NUMBER()
            OVER (PARTITION BY emu.event_id ORDER BY emu.id) AS row_num
        FROM event_medication_use emu
        ) emu2
        ON emu2.event_id = e.id And emu2.row_num = 1
    JOIN ophdrprescription_dispense_condition odc
        ON odc.id = emu2.dispense_condition_id
    JOIN ophdrprescription_dispense_location odl
        ON odl.id = emu2.dispense_location_id
    LEFT JOIN ophdrprescription_edit_reasons oer
        ON oer.id = eod.edit_reason_id;

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW v_prescription_items AS
SELECT
    eod.event_id AS 'prescription_event_id',
    emu.id AS 'prescription_item_id',
    emu.medication_id AS 'medication_id',
    m.preferred_term AS 'medication_preferred_term',
    IFNULL(eye.name, 'N/A') AS 'laterality',
    emu.form_id,
    mf.term AS 'form_name',
    emu.dose,
    emu.dose_unit_term,
    emu.route_id,
    mr.term AS 'route_name',
    emu.frequency_id,
    mfr.term AS 'frequency_name',
    emu.duration_id,
    md.name AS 'duration_name',
    emu.dispense_location_id,
    odl.name AS 'dispense_location_name',
    emu.dispense_condition_id,
    odc.name AS 'dispense_condition_name',
    emu.start_date,
    emu.end_date,
    emu.stop_reason_id,
    omsr.name AS 'stop_reason_name',
    eod.comments,
    IF(oit2.id IS NULL, 0, 1) AS 'has_taper'
FROM et_ophdrprescription_details eod
         JOIN event e
              ON e.id = eod.event_id
         JOIN event_medication_use emu
              ON emu.event_id = e.id
         LEFT JOIN medication_form mf
                   ON mf.id = emu.form_id
         LEFT JOIN medication_route mr
                   ON mr.id = emu.route_id
         LEFT JOIN medication_frequency mfr
                   ON mfr.id = emu.frequency_id
         LEFT JOIN medication_duration md
                   ON md.id = emu.duration_id
         LEFT JOIN ophciexamination_medication_stop_reason omsr
                   ON omsr.id = emu.stop_reason_id
         LEFT JOIN eye
                   ON eye.id = emu.laterality
         JOIN medication m
              ON m.id = emu.medication_id
         JOIN ophdrprescription_dispense_condition odc
              ON odc.id = emu.dispense_condition_id
         JOIN ophdrprescription_dispense_location odl
              ON odl.id = emu.dispense_location_id
         LEFT JOIN ophdrprescription_edit_reasons oer
                   ON oer.id = eod.edit_reason_id
         LEFT JOIN
     (
         SELECT oit.item_id, MIN(oit.id) AS 'id'
         FROM ophdrprescription_item_taper oit
         GROUP BY oit.item_id
     ) oit2
     ON oit2.item_id = emu.id;

CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW v_prescription_item_tapers AS
SELECT
    emu.medication_id AS 'prescription_id',
    item_id,
    emu.dose,
    emu.dose_unit_term,
    emu.frequency_id,
    mfr.term AS 'frequency_name',
    emu.duration_id,
    md.name AS 'duration_name'
FROM ophdrprescription_item_taper oit
JOIN event_medication_use emu
    ON oit.item_id = emu.id
LEFT JOIN medication_frequency mfr
    ON mfr.id = emu.frequency_id
LEFT JOIN medication_duration md
    ON md.id = emu.duration_id;

CREATE USER $USERNAME@'%' IDENTIFIED BY '$PASSWORD';

GRANT SELECT ON openeyes.v_prescriptions TO $USERNAME@'%';

GRANT SELECT ON openeyes.v_prescription_items TO $USERNAME@'%';

GRANT SELECT ON openeyes.v_prescription_item_tapers TO $USERNAME@'%';

COMMIT;

FLUSH PRIVILEGES;
EOF
