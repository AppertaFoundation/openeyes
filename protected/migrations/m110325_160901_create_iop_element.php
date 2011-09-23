<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class m110325_160901_create_iop_element extends CDbMigration
{
    public function up()
    {
		$this->addColumn('element_intraocular_pressure', 'right_iop', 'tinyint');
		$this->addColumn('element_intraocular_pressure', 'left_iop', 'tinyint');
		
		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class', 
				array(':name'=>'Intraocular pressure', ':class'=>'ElementIntraocularPressure'))
			->queryRow();

		$this->insert('possible_element_type', array(
			'event_type_id' => 1,
			'element_type_id' => $elementType['id'],
			'num_views' => 1,
			'order' => 10
		));
    }

    public function down()
    {
		$this->dropColumn('element_intraocular_pressure', 'right_iop');
		$this->dropColumn('element_intraocular_pressure', 'left_iop');

		$elementType = $this->dbConnection->createCommand()
			->select('id')
			->from('element_type')
			->where('name=:name AND class_name=:class', 
				array(':name'=>'Intraocular pressure', ':class'=>'ElementIntraocularPressure'))
			->queryRow();

		$this->delete('possible_element_type', 'element_type_id = :id',
			array(':id' => $elementType['id'])
		);
    }
}
