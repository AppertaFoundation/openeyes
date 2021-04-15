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

namespace OEModule\OphCiExamination\models;


/**
 * This is the model class for table "ophciexamination_risk".
 *
 * Could not be named Risk due to conflicts with core class (that is left in place as a view on this model)
 *
 * @property int $id
 * @property string $name
 * @property int $institution_id
 * @property int $display_order
 * @property boolean $is_other
 * @property \MedicationSet[] $medicationSets
 * @property \Tag[] $tags
 */
class OphCiExaminationRisk extends \BaseActiveRecordVersioned
{
    protected $auto_update_relations = true;

    use \MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return \ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'risk_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
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
        return 'ophciexamination_risk';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, medicationSets, gender, age_min, age_max, display_on_whiteboard', 'safe'),
            array('id, name', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'medicationSets' => array(self::MANY_MANY, \MedicationSet::class, 'ophciexamination_risk_tag(risk_id, medication_set_id)'),
            'risk_institution' => array(self::HAS_MANY, 'OphCiExaminationRisk_Institution', 'risk_id'),
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophciexamination_risk_institution(risk_id,institution_id)'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'episodeStatus' => array(self::BELONGS_TO, 'EpisodeStatus', 'episode_status_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'institutions.name' => 'Institutions',
            'medicationSets' => 'Drug sets',
            'display_on_whiteboard' => 'Display on Whiteboard',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $tag_ids
     * @return array|\CActiveRecord[]|mixed|null
     *
     * @deprecated use findForRefSetIds
     */

    public static function findForTagIds($tag_ids)
    {
        $criteria = new \CDbCriteria();
        $criteria->addInCondition('tags.id', $tag_ids);
        return static::model()->with(array(
            'tags' => array(
                'select' => false,
                'joinType' => 'INNER JOIN',
            )))->findAll($criteria);
    }

    /**
     * @param array $medication_set_ids
     * @return static[]
     */

    public static function findForMedicationSetIds($medication_set_ids)
    {
        $criteria = new \CDbCriteria();
        $criteria->addInCondition('medicationSets.id', $medication_set_ids);
        $criteria->addCondition("active = 1");
        return static::model()->with(array(
                'medicationSets' => array(
                    'select' => false,
                    'joinType' => 'INNER JOIN',
                )))->findAll($criteria);
    }

    /**
     * @TODO: replace with DB property
     * @return bool
     */
    public function isOther()
    {
        return $this->name === 'Other';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
