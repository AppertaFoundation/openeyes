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

class m210728_084842_add_esign_element extends OEMigration
{
    private const ELEMENT_TBL = 'et_ophcocvi_esign';
    private const ITEM_TBL = 'ophcocvi_signature';
    private const RETIRED_ET_CLASS = 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature';

    public function safeUp()
    {
        $this->createOETable(
            self::ELEMENT_TBL,
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL',
            ],
            true
        );

        $this->createOETable(
            self::ITEM_TBL,
            [
                'id' => 'pk',
                'element_id' => 'INT(11) NOT NULL',
                'type' => 'TINYINT UNSIGNED',
                'signature_file_id' => 'INT(10) UNSIGNED NULL',
                'signed_user_id' => 'INT(10) UNSIGNED NULL',
                'signatory_role' => 'VARCHAR(64) NOT NULL',
                'signatory_name' => 'VARCHAR(64) NOT NULL',
                'timestamp' => 'INT(11) NOT NULL',
            ],
            true
        );

        $this->addForeignKey('fk_ophcocvi_signature_etid', self::ITEM_TBL, 'element_id', self::ELEMENT_TBL, 'id');
        $this->addForeignKey('fk_et_ophcocvi_esign_event_id', self::ELEMENT_TBL, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_ophcocvi_signature_sfile_id', self::ITEM_TBL, 'signature_file_id', 'protected_file', 'id');
        $this->addForeignKey('fk_ophcocvi_signature_suser_id', self::ITEM_TBL, 'signed_user_id', 'user', 'id');

        $this->createElementType('OphCoCvi', 'E-Sign', array(
            'class_name' => 'Element_OphCoCvi_Esign',
            'display_order' => 60,
            'parent_class' => null,
            "default" => 1,
            "required" => 1,
        ));

        $this->deleteElementType('OphCoCvi', self::RETIRED_ET_CLASS);
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphCoCvi");
        $this->insert('element_type', array('name' => 'Consent Signature','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature', 'event_type_id' => $event_type_id, 'display_order' => 30, 'required' => 1));
        $this->deleteElementType('OphCoCvi', 'Element_OphCoCvi_Esign');
        $this->dropForeignKey('fk_ophcocvi_signature_etid', self::ITEM_TBL);
        $this->dropForeignKey('fk_et_ophcocvi_esign_event_id', self::ELEMENT_TBL);
        $this->dropForeignKey('fk_ophcocvi_signature_sfile_id', self::ITEM_TBL);
        $this->dropForeignKey('fk_ophcocvi_signature_suser_id', self::ITEM_TBL);
        $this->dropOETable(self::ITEM_TBL, true);
        $this->dropOETable(self::ELEMENT_TBL, true);
    }
}
