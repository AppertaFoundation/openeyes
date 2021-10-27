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

class m211025_110020_migrate_disorders_and_sections extends \OEMigration
{
    public function safeUp()
    {
        $assignment_table = 'et_ophcocvi_clinicinfo_disorder_assignment';
        $section_table = 'ophcocvi_clinicinfo_disorder_section';
        $disoder_table = 'ophcocvi_clinicinfo_disorder';

        $query = <<<EOT
UPDATE
$assignment_table assignment
	LEFT JOIN $disoder_table old_disorder ON assignment.`ophcocvi_clinicinfo_disorder_id` = old_disorder.id AND old_disorder.`event_type_version` = 0
	LEFT JOIN $section_table old_section ON old_section.id = old_disorder.`section_id`
	LEFT JOIN $disoder_table d2 ON d2.id = ( 
			SELECT new_disorder.id 
			FROM $disoder_table new_disorder 
			LEFT JOIN $section_table new_section ON new_section.id = new_disorder.`section_id` AND new_disorder.`event_type_version` = 1 
			WHERE new_disorder.`name` = old_disorder.`name` AND new_section.`name` = old_section.`name` AND new_disorder.`patient_type` = old_disorder.`patient_type` 	
	)
SET assignment.ophcocvi_clinicinfo_disorder_id = d2.id
WHERE old_disorder.id IS NOT NULL AND d2.id IS NOT NULL;
EOT;
        $this->execute($query);

        // there are some disorders that have no v1 version
        $query = <<<EOT
            SELECT d.name, a.* 
            FROM `et_ophcocvi_clinicinfo_disorder_assignment_MEH` a
            JOIN `ophcocvi_clinicinfo_disorder_MEH` d ON d.id = a.`ophcocvi_clinicinfo_disorder_id`
            WHERE d.`event_type_version` = 0;
EOT;

        $result = $this->dbConnection->createCommand($query)->queryAll();

        echo '<pre>' . print_r($result, true) . '</pre>';

        return false;
    }

    private function getDisorderIdFromMap(array $entry)
    {
        $name = '';

        switch ($entry['disorder_name']) {

            case 'age-related macular degeneration - atrophic / geographic macular atrophy':
                $name = 'age-related macular degeneration - choroidal neovascularisation (dry)';
                break;

            case 'age-related macular degeneration - subretinal neovascularisation':
                $name = 'age-related macular degeneration - choroidal neovascularisation (wet)';
                break;

            case 'retinopathy of prematurity':
                // In DB there are 2 "retinopathy of prematurity" entries,
                // |             name            | patient_type | event_type_version |
                // |  retinopathy of prematurity |       0      |          0         |
                // |  retinopathy of prematurity |       1      |          1         |

                // basically for patient_type 0 we do not have v1
                $name = ' ??? ';
                break;

            case 'congenital CNS malformations':
                // only one entry in DB
                // |             name             | patient_type | event_type_version |
                // | congenital CNS malformations |       0      |          0         |
                $name = ' ??? ';
                break;

            case 'congenital eye malformations':
                // only one entry in DB
                // |             name             | patient_type | event_type_version |
                // | congenital eye malformations |       0      |          0         |
                $name = ' ??? ';
                break;
        }

        $entry['disorder_name'] = $name;
        return $this->getNewDisorderId($entry);
    }

    public function safeDown()
    {
        echo "The m211025_110020_migrate_disorders_and_sections migration does not support down.";
    }
}
