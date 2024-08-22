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

class m200219_120605_more_colourvision_options extends OEMigration
{

    protected function setDisplayOrderToMax($table, $conditions)
    {
        $new_display_order = $this->getDbConnection()->createCommand("select count(*) as ct from $table")->queryRow()['ct'];
        $this->getDbConnection()->createCommand("update $table set display_order = $new_display_order where $conditions")->execute();
    }

    protected function removeMethod($method_name)
    {
        $id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->where('name = :name', [':name' => $method_name])
            ->from('ophciexamination_colourvision_method')
            ->queryScalar();

        $this->delete('ophciexamination_colourvision_value', 'method_id = :method_id', [':method_id' => $id]);
        $this->delete('ophciexamination_colourvision_method', 'id = :id', [':id' => $id]);
    }

    public function safeUp()
    {
        $this->initialiseData(dirname(__FILE__));
        $this->setDisplayOrderToMax('ophciexamination_colourvision_method', "name = 'Red desaturation'");
    }

    public function safeDown()
    {
        parent::safeDown();
        $data_path = $this->getDataDirectory(dirname(__FILE__));
        $methods_file = glob($data_path . '*ophciexamination_colourvision_method.csv')[0];
        $fh = fopen($methods_file, 'r');
        $columns = fgetcsv($fh);
        $name_col = array_search('name', $columns);
        if ($name_col === false) {
            throw new Exception('cannot resolve name column in data file');
        }

        while (($row = fgetcsv($fh)) !== false) {
            $this->removeMethod($row[$name_col]);
        }
        fclose($fh);

        $this->setDisplayOrderToMax('ophciexamination_colourvision_method', "name = 'Red desaturation'");
    }
}
