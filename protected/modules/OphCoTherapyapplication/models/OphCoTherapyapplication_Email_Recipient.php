<?php

/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCoTherapyapplication_Email_Recipient extends BaseActiveRecordVersioned
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ophcotherapya_email_recipient';
    }

    public function rules()
    {
        return array(
            array('institution_id, site_id, recipient_name, recipient_email, type_id', 'safe'),
            array('institution_id, recipient_name, recipient_email', 'required'),
            array('recipient_email', 'email'),
        );
    }

    public function relations()
    {
        return array(
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'type' => array(self::BELONGS_TO, 'OphCoTherapyapplication_Email_Recipient_Type', 'type_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'type_id' => 'Letter types',
            'recipient_name' => 'Recipient name',
            'recipient_email' => 'Recipient email',
        );
    }

    public function isAllowed()
    {
        if (SettingMetadata::model()->getSetting('restrict_email_domains')) {
            return in_array(strtolower(preg_replace('/^.*?@/', '', $this->recipient_email)), SettingMetadata::model()->getSetting('restrict_email_domains'));
        }

        return true;
    }

    protected function beforeValidate()
    {
        if (!$this->isAllowed()) {
            $this->addError('recipient_email', 'Recipient email is not in the list of allowed domains');
        }

        return parent::beforeValidate();
    }
}
