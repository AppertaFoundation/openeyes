<?php
/**
 * OpenEyes.
 *
 * (C) Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class PrescriptionVerificationCommand
 *
 * This class creates a zz_* table and a CSV containing the prescription related data. This should be run
 * prior to the DM+D Data Migration.
 */
class PrescriptionVerificationCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        echo $this->getHelp();
    }

    public function getHelp()
    {
        return <<<EOH


Prescription Verification Dump

This command creates a verification dump of the prescription data depending on the parameters provided.

USAGE
  php yiic.prescriptionverification [action]

Following actions are available:

 - prescriptionBeforeMigration      : Creates a dump of the prescription data prior to the dmd data import.
 - prescriptionAfterMigration       : Creates a dump of the prescription data once the dmd data import process finishes.
 - help                             : Display this help and exit

EOH;
    }

    public function actionPrescriptionBeforeMigration()
    {
        $dump = 'zz_dm_d_migration_task_1_before_prescription_data';

        try {
            echo "Drop the table if it exists\n";
            $tableSchema = Yii::app()->db->schema->getTable($dump);
            if ($tableSchema !== null) {
                $deleteTableSql = Yii::app()->db->createCommand('
DROP TABLE ' . $dump);
                $deleteTableSql->execute();
            }

            $sql = Yii::app()->db->createCommand('
CREATE TABLE ' . $dump . '
    AS
    SELECT
        ev.event_date                          as event_date_and_time,
        ev.id                                  as event_id,
        p.hos_num                              as patient_hospital_number,
        p.dob                                  as patient_dob,
        c.last_name                            as patient_last_name,
        c.first_name                           as patient_first_name,
        pres.line_internal_id,
        pres.taper_internal_id ,
        d.tallman                              as drug,
        pres.dose                              as dose,
        dr.name                                as route,
        dro.name                               as laterality,
        df.name                                as frequency,
        dd.name                                as duration,
        odc.name                               as dispense_condition,
        odl.name                               as dispense_location,
        pres.comments                          as line_comment,
        eod.comments                           as prescription_comment,
        ev.created_date                        as event_created_date_and_time,
        CONCAT(u.first_name, " ", u.last_name) as created_user_forename_surname
    FROM et_ophdrprescription_details eod
        JOIN event ev
            ON ev.id = eod.event_id
        JOIN episode ep
            ON ev.episode_id = ep.id
        JOIN patient p
            ON p.id = ep.patient_id
        JOIN contact c
            ON c.id = p.contact_id
        LEFT JOIN (
            SELECT
                eod1.event_id,
                oi.id as line_internal_id,
                null as taper_internal_id,
                oi.drug_id,
                oi.dose,
                oi.frequency_id,
                oi.duration_id,
                oi.route_id,
                oi.route_option_id,
                oi.dispense_condition_id,
                oi.dispense_location_id,
                oi.comments
            FROM et_ophdrprescription_details eod1
                JOIN ophdrprescription_item oi
                    ON oi.prescription_id = eod1.id
            UNION ALL
            SELECT
                emu.event_id,
                oi1.id,
                oit.id,
                null,
                oit.dose,
                oit.frequency_id,
                oit.duration_id,
                null,
                null,
                null,
                null,
                null
            FROM  event_medication_use emu
                JOIN ophdrprescription_item oi1
                    ON oi1.id = emu.temp_prescription_item_id
                JOIN ophdrprescription_item_taper oit
                    ON oit.item_id = emu.id
        ) pres
            ON pres.event_id = eod.event_id
        LEFT JOIN drug d
            ON d.id = pres.drug_id
        LEFT JOIN drug_route dr
            ON dr.id = pres.route_id
        LEFT JOIN drug_route_option dro
            ON dro.id = pres.route_option_id
        LEFT JOIN drug_frequency df
            ON df.id = pres.frequency_id
        LEFT JOIN drug_duration dd
            ON dd.id = pres.duration_id
        LEFT JOIN ophdrprescription_dispense_condition odc
            ON odc.id = pres.dispense_condition_id
        LEFT JOIN ophdrprescription_dispense_location odl
            ON odl.id = pres.dispense_location_id
        LEFT JOIN user u
            ON u.id = eod.created_user_id
    ORDER BY
        event_date_and_time,
        event_created_date_and_time,
        event_id,
        line_internal_id,
        taper_internal_id;
');

            echo "Creating table " . $dump . "\n";
            $sql->execute();
            echo "Table created.\n";

            $f = fopen(Yii::app()->basePath . '/data/'. $dump .'.csv', 'w');
            if (!$f) {
                die("Failed to open for writing\n");
            }

            echo "Saving the table data in the CSV format\n";
            $db = Yii::app()->db;
            $cmd = $db->createCommand()->select('*')->from($dump);
            $rows = $cmd->queryAll();

            $first = true;
            foreach ($rows as $row) {
                if ($first) {
                    fputcsv($f, array_keys($row));
                    $first = false;
                }
                if (!fputcsv($f, $row)) {
                    die("Failed to write CSV row\n");
                }
            }

            if (!fflush($f)) {
                die("Flush failed");
            }
            fclose($f);
            echo "Data Saved.\n";
        } catch (CDbException $e) {
            var_dump($e->errorInfo);
        }
    }

    public function actionPrescriptionAfterMigration()
    {
        $dump = 'zz_dm_d_migration_task_2_after_prescription_data';

        try {
            echo "Drop the table if it exists\n";
            $tableSchema = Yii::app()->db->schema->getTable($dump);
            if ($tableSchema !== null) {
                $deleteTableSql = Yii::app()->db->createCommand('
DROP TABLE ' . $dump);
                $deleteTableSql->execute();
            }

            $sql = Yii::app()->db->createCommand('
CREATE TABLE ' . $dump . '
    AS
    SELECT
        ev.event_date                          as event_date_and_time,
        ev.id                                  as event_id,
        p.hos_num                              as patient_hospital_number,
        p.dob                                  as patient_dob,
        c.last_name                            as patient_last_name,
        c.first_name                           as patient_first_name,
        pres.line_internal_id,
        pres.taper_internal_id,
        m.preferred_term                       as drug,
        pres.dose,
        mr.term                                as route,
        ml.name                                as laterality,
        mf.term                                as frequency,
        md.name                                as duration,
        odc.name                               as dispense_condition,
        odl.name                               as dispense_location,
        pres.comments                          as line_comment,
        eod.comments                           as prescription_comment,
        ev.created_date                        as event_created_date_and_time,
        CONCAT(u.first_name, " ", u.last_name) as created_user_forename_surname
    FROM et_ophdrprescription_details eod
        JOIN event ev
            ON ev.id = eod.event_id
        JOIN episode ep
            ON ev.episode_id = ep.id
        JOIN patient p
            ON p.id = ep.patient_id
        JOIN contact c
            ON c.id = p.contact_id
        LEFT JOIN (
            SELECT
                emu.event_id,
                emu.id as line_internal_id,
                null as taper_internal_id,
                emu.medication_id,
                emu.dose,
                emu.frequency_id,
                emu.duration_id,
                emu.route_id,
                emu.laterality,
                emu.dispense_condition_id,
                emu.dispense_location_id,
                emu.comments
            FROM event_medication_use emu
            UNION ALL
            SELECT
                emu.event_id,
                emu.id,
                oit.id,
                null,
                oit.dose,
                oit.frequency_id,
                oit.duration_id,
                null,
                null,
                null,
                null,
                null
            FROM event_medication_use emu
            JOIN ophdrprescription_item_taper oit
                ON oit.item_id = emu.id
        ) pres
            ON pres.event_id = eod.event_id
        LEFT JOIN medication m
            ON m.id = pres.medication_id
        LEFT JOIN medication_route mr
            ON mr.id = pres.route_id
        LEFT JOIN medication_laterality ml
            ON ml.id = pres.laterality
        LEFT JOIN medication_frequency mf
            ON mf.id = pres.frequency_id
        LEFT JOIN medication_duration md
            ON md.id = pres.duration_id
        LEFT JOIN ophdrprescription_dispense_condition odc
            ON odc.id = pres.dispense_condition_id
        LEFT JOIN ophdrprescription_dispense_location odl
            ON odl.id = pres.dispense_location_id
        LEFT JOIN user u
            ON u.id = eod.created_user_id
    ORDER BY
        event_date_and_time,
        event_created_date_and_time,
        event_id,
        line_internal_id,
        taper_internal_id
');

            echo "Creating table " . $dump . "\n";
            $sql->execute();
            echo "Table created.\n";

            $f = fopen(Yii::app()->basePath . '/data/'. $dump .'.csv', 'w');
            if (!$f) {
                die("Failed to open for writing\n");
            }

            echo "Saving the table data in the CSV format\n";
            $db = Yii::app()->db;
            $cmd = $db->createCommand()->select('*')->from($dump);
            $rows = $cmd->queryAll();

            $first = true;
            foreach ($rows as $row) {
                if ($first) {
                    fputcsv($f, array_keys($row));
                    $first = false;
                }
                if (!fputcsv($f, $row)) {
                    die("Failed to write CSV row\n");
                }
            }

            if (!fflush($f)) {
                die("Flush failed");
            }
            fclose($f);
            echo "Data Saved.\n";
        } catch (CDbException $e) {
            var_dump($e->errorInfo);
        }
    }
}
