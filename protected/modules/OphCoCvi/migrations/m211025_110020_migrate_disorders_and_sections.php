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
    private array $disorder_id_cache = [];

    const ASSIGNMENT_TBL = "et_ophcocvi_clinicinfo_disorder_assignment_MEH";
    const SECTION_TBL = "ophcocvi_clinicinfo_disorder_section_MEH";
    const DISORDER_TBL = "ophcocvi_clinicinfo_disorder_MEH";

    public function safeUp()
    {

        // get all elements belong to event_type_version 0 sections
        $command = \Yii::app()->db->createCommand()
            ->select('a.id as assignment_id, s.id as section_id, s.name as section_name, d.id as disorder_id, d.name as disorder_name, d.patient_type, element_id')
            ->from(self::ASSIGNMENT_TBL . " a")
            ->join(self::DISORDER_TBL . ' d', 'a.ophcocvi_clinicinfo_disorder_id = d.id')
            ->join(self::SECTION_TBL . ' s', 'd.section_id = s.id')
            ->where('s.event_type_version = 0');

        $iterator = new \QueryIterator($command, 2000);

        $manual_mapping_needed = [];

        foreach ($iterator as $chunk) {
            $update = [];
            foreach ($chunk as $entry) {

                $new_disorder_id = $this->getNewDisorderId($entry);
                if (!$new_disorder_id) {
                    if (!in_array($entry['disorder_name'], $manual_mapping_needed)) {
                        $manual_mapping_needed[] = $entry['disorder_name'];
                    }

                    // Manual mapping
                    $new_disorder_id = $this->getDisorderIdFromMap($entry);

                    echo "Manual mapping required: {$entry['disorder_name']}\n";
                    echo " ===> new id {$new_disorder_id}\n";
                }

                if ($new_disorder_id) {
                    $update[$new_disorder_id][] = $entry['assignment_id'];
                } else {
                   //echo "Can't map '{$entry['disorder_name']}' disorder, assignment: {$entry['assignment_id']}\n";
                }
            }

            $this->updateAssignments($update);
        }

        return true;
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

    /**
     * @param array $entry
     */
    private function getNewDisorderId(array $entry):? int
    {
        $key = str_replace(' ', '', "{$entry['section_name']}-{$entry['disorder_name']}-{$entry['patient_type']}");

        if (isset($this->disorder_id_cache[$key]) && $this->disorder_id_cache[$key]) {
            return $this->disorder_id_cache[$key];
        }

        $new_disorder_id = \Yii::app()->db->createCommand()
            ->select('d.id')
            ->from(self::DISORDER_TBL . ' d')
            ->join(self::SECTION_TBL . ' s', 'd.section_id = s.id')
            ->where('s.name = :s_name AND d.name = :d_name AND s.patient_type = :p_type AND s.event_type_version = 1')
                ->bindParam(':s_name', $entry['section_name'])
                ->bindParam(':d_name', $entry['disorder_name'])
                ->bindParam(':p_type', $entry['patient_type'])
           ->queryScalar();

        $this->disorder_id_cache[$key] = $new_disorder_id;

        return $new_disorder_id;
    }

    private function updateAssignments(array $data)
    {
        foreach ($data as $new_disorder_id => $assignment_ids) {
            echo "\n";
            echo "Updating " . count($assignment_ids) . " rows ... ";

            //echo 'UPDATE ' . self::ASSIGNMENT_TBL . ' SET ophcocvi_clinicinfo_disorder_id = ' . $new_disorder_id . ' WHERE id IN (' . implode(', ', array_unique($assignment_ids)) . ')';
            ob_start();
            $this->execute('UPDATE ' . self::ASSIGNMENT_TBL . ' SET ophcocvi_clinicinfo_disorder_id = :d_id WHERE id IN (' . implode(', ', array_unique($assignment_ids)) . ')', [
                ':d_id' => $new_disorder_id
            ]);
            ob_end_clean();
            echo "done.\n";
        }
    }

    public function safeDown()
    {
        echo "The m211025_110020_migrate_disorders_and_sections migration does not support down.";
    }
}
