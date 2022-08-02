<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m220727_101010_add_icd10_to_disorder_admin extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            "disorder",
            "icd10_code",
            "VARCHAR(10) NULL",
            true
        );
        $this->addOEColumn(
            "disorder",
            "icd10_term",
            "VARCHAR(255) NULL",
            true
        );

        $this->execute("CREATE OR REPLACE VIEW `v_patient_diagnoses` AS
        SELECT
            `p`.`patient_id` AS `patient_id`,
            `p`.`eye_id` AS `eye_id`,
            `p`.`side` AS `side`,
            `d`.`id` AS `disorder_id`,
            `d`.`term` AS `disorder_term`,
            `d`.`fully_specified_name` AS `disorder_fully_specified`,
            `d`.`icd10_code` AS `icd10_code`,
            `d`.`icd10_term` AS `icd10_term`,
            `p`.`disorder_date` AS `disorder_date`,
            `d`.`aliases` AS `disorder_aliases`,
            `s`.`name` AS `specialty`
        FROM
            (((`v_patient_episodes` `p`
            JOIN `disorder` `d` ON ((`p`.`disorder_id` = `d`.`id`)))
            LEFT JOIN `subspecialty` `ss` ON ((`ss`.`id` = `p`.`subspecialty_id`)))
            LEFT JOIN `specialty` `s` ON ((`s`.`id` = `ss`.`specialty_id`)))
        UNION SELECT
            `sd`.`patient_id` AS `patient_id`,
            `sd`.`eye_id` AS `eye_id`,
            (CASE `sd`.`eye_id`
                WHEN 1 THEN 'L'
                WHEN 2 THEN 'R'
                WHEN 3 THEN 'B'
            END) AS `side`,
            `d`.`id` AS `disorder_id`,
            `d`.`term` AS `disorder_term`,
            `d`.`fully_specified_name` AS `disorder_fully_specified`,
            `d`.`icd10_code` AS `icd10_code`,
            `d`.`icd10_term` AS `icd10_term`,
            `sd`.`date` AS `disorder_date`,
            `d`.`aliases` AS `disorder_aliases`,
            `s`.`name` AS `specialty`
        FROM
            ((`secondary_diagnosis` `sd`
            JOIN `disorder` `d` ON ((`d`.`id` = `sd`.`disorder_id`)))
            LEFT JOIN `specialty` `s` ON ((`s`.`id` = `d`.`specialty_id`)))
        ORDER BY `patient_id`");
    }

    public function down()
    {
        \Yii::import('application.migrations.*');
        $this->execute(\m190403_161000_add__more_report_views::$diagnosis_view_definition);

        $this->dropOEColumn(
            "disorder",
            "icd10_code",
            true
        );
        $this->dropOEColumn(
            "disorder",
            "icd10_term",
            true
        );
    }
}
