<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "medication_drug". This provides a wider scope of drug look up than the original
 * Drug model, which only contains the data that the Institution prescribes.
 *
 * The followings are the available columns in table 'medication_drug':
 *
 * @property int $id
 * @property string $name
 * @property string $aliases
 * @property string $external_id
 * @property string $external_source
 * @property Tag[] $tags
 * @deprecated See Medication
 */
class MedicationDrug extends BaseActiveRecordVersioned
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Drug the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_drug';
    }

    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return array(
            'TaggedActiveRecordBehavior' => 'TaggedActiveRecordBehavior'
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name', 'required'),
            array('external_code', 'safe'),
            array('external_source', 'safe'),
            array('aliases', 'safe'),
            array('tags', 'safe'),
        );
    }

    /**
     * @return array list of attribute labels
     */
    public function attributeLabels()
    {
        return array(
            'name' => 'Name',
            'external_code' => 'Source Code',
            'external_source' => 'Source',
            'aliases' => 'Aliases',
            'tags' => 'Tags',
        );
    }

    /**
     * @inheritdoc
     */

    public function relations()
    {
        return array(
            'tags' => array(self::MANY_MANY, 'Tag', 'medication_drug_tag(medication_drug_id, tag_id)')
        );
    }

    public function __toString()
    {
        return $this->name;
    }

}
