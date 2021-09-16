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

class m160826_143933_change_element_order extends CDbMigration
{

    protected $element_classes = array(
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo');

    public function up()
    {
        $display_order = 10;
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update(
                'element_type',
                array('display_order' => $display_order += 10),
                'id = :et_id',
                array(':et_id' => $element['id'])
            );
        }

    }

    public function down()
    {
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update(
                'element_type',
                array('display_order' => 1),
                'id = :et_id',
                array(':et_id' => $element['id'])
            );
        }
    }
}
