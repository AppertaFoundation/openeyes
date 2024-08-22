<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 */
class OphTrOperationnote_AttributeTest extends ActiveRecordTestCase
{
    use WithTransactions;

    public function getModel()
    {
        return OphTrOperationnote_Attribute::model();
    }
    /**
     * @covers OphTrOperationnote_Attribute::model
     *
     */
    public function testModel()
    {
        $this->assertEquals('OphTrOperationnote_Attribute', get_class(OphTrOperationnote_Attribute::model()), 'Class name should match model.');
    }

    /**
     * @covers OphTrOperationnote_Attribute::getItemsAdminLink
     */

    public function testGetItemsAdminLink()
    {
        $record = new OphTrOperationnote_Attribute();
        $record->id = 1;
        $this->assertEquals('<a href="/OphTrOperationnote/attributeOptionsAdmin/index?attribute_id=1">Manage items</a>', $record->getItemsAdminLink());
    }

    private function getTestRecord()
    {
        $record = new OphTrOperationnote_Attribute();
        $record->proc_id = 1;
        $record->name = "Test Attribute";
        $record->label = "test_attr";
        return $record;
    }

    private function getSecondTestRecord()
    {
        $record = new OphTrOperationnote_Attribute();
        $record->proc_id = 1;
        $record->name = "Test Attribute 2";
        $record->label = "test_attr2";
        return $record;
    }

    /**
     * @covers OphTrOperationnote_Attribute::beforeDelete
     */

    public function testBeforeDelete()
    {
        $record = $this->getTestRecord();
        $record->save();
        $opt1 = new OphTrOperationnote_AttributeOption();
        $r_id = $record->id;
        $opt1->attribute_id = $r_id;
        $opt1->value = 5;
        $opt1->save();

        $record->delete();
        $this->assertEquals(0, Yii::app()->db->createCommand('select count(*) from ophtroperationnote_attribute_option where attribute_id = ?')->queryScalar(array($r_id)));
    }

    /**
     * @covers OphTrOperationnote_Attribute::beforeValidate
     */

    public function testBeforeValidate()
    {
        $record1 = $this->getTestRecord();
        $record1->save();

        $record2 = $this->getSecondTestRecord();
        $record2->save(true);

        $this->assertEquals($record2->display_order, $record1->display_order + 1);
        $this->assertIsInt($record2->is_multiselect);
    }

    /**
     * @covers OphTrOperationnote_Attribute::afterFind
     */

    public function testAfterFind()
    {

        $record = $this->getTestRecord();
        $record->save();

        $r_id = $record->id;

        unset($record);

        $record = OphTrOperationnote_Attribute::model()->findByPk($r_id);
        $this->assertIsBool($record->is_multiselect);
    }
}
