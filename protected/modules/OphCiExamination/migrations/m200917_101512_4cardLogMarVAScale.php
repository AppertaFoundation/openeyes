<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200917_101512_4cardLogMarVAScale extends OEMigration
{
    protected const UNIT_NAME = '4 card logMAR';

    public function safeUp()
    {
        $this->insert('ophciexamination_visual_acuity_unit', [
            'name' => self::UNIT_NAME,
            'active' => '1',
            'complex_only' => '1']
        );
        $unit_id = $this->getInsertId('ophciexamination_visual_acuity_unit');

        $base_value = 20;
        $values = [
            ['NPL', 1],
            ['PL', 2],
            ['HM', 3],
            ['CF', 4]
        ];
        for ($log_mar = 1.8; $log_mar > -0.3; $log_mar -= 0.1) {
            foreach ([[0, 0], [0.025, 1], [0.05, 3], [0.075, 4]] as $v) {
                // some awkward manipulation to avoid negative zero or no decimal padding
                $lm_val = round($log_mar - $v[0], 3);
                $lm_val = $lm_val == 0 ? "0.0" : rtrim(sprintf("%.3f", $lm_val), "0");
                if ($lm_val[-1] === ".") {
                    $lm_val .= "0";
                }
                $values[] = [$lm_val, $base_value + $v[1]];
            }
            $base_value += 5;
        }

        // final value for range
        $values[] = ['-0.3', 125];

        foreach ($values as $row) {
            echo "\nInserting value=" . $row[0] . " :  base_value=" . $row[1] . "\n";
            $this->insert('ophciexamination_visual_acuity_unit_value', [
                'unit_id' => $unit_id,
                'value' => $row[0],
                'base_value' => $row[1]
            ]);
        }

        return true;
    }

    public function safeDown()
    {
        $unit_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophciexamination_visual_acuity_unit')
            ->where('name = :name', [':name' => self::UNIT_NAME])
            ->queryScalar();

        $this->delete('ophciexamination_visual_acuity_unit_value', 'unit_id = :unit_id', [':unit_id' => $unit_id]);
        $this->delete('ophciexamination_visual_acuity_unit', 'id = :id', [':id' => $unit_id]);

        return true;
    }
}
