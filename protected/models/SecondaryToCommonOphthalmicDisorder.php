<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "secondaryto_common_oph_disorder".
 * This model allows for the definition of "secondary to" options for disorders specified in CommonOphthalmicDisorder
 * They should be provided as choices in disorder widgets that use the common disorder drop downs in diagnoses selection.
 *
 * The followings are the available columns in table 'common_ophthalmic_disorder':
 *
 * @property int $id
 * @property int $disorder_id
 * @property int $finding_id
 * @property int $parent_id
 * @property string $letter_macro_text
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 * @property Finding $finding
 * @property CommonOphthalmicDisorder $parent
 */
class SecondaryToCommonOphthalmicDisorder extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return $this->tableName().'_id';
    }


    const SELECTION_LABEL_FIELD = 'disorder_id';
    /**
     * Returns the static model of the specified AR class.
     *
     * @return SecondaryToCommonOphthalmicDisorder the static model class
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
        return 'secondaryto_common_oph_disorder';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('parent_id', 'required'),
                array('disorder_id, finding_id, parent_id', 'length', 'max' => 10),
                array('letter_macro_text', 'length', 'max' => 255),
                array('id, disorder_id, finding_id, parent_id, letter_macro_text, created_date, created_user_id, last_modified_date, last_modified_user_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
                array('id, disorder_id, finding_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id', 'condition' => 'disorder.active = 1'),
                'finding' => array(self::BELONGS_TO, 'Finding', 'finding_id', 'condition' => 'finding.active = 1'),
                'parent' => array(self::BELONGS_TO, 'CommonOphthalmicDisorder', 'parent_id'),
                'institutions' => array(self::MANY_MANY, 'Institution', $this->tableName().'_institution('.$this->tableName().'_id, institution_id)'),
        );
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'disorder_id' => 'Disorder',
            'finding_id' => 'Finding',
            'parent_id' => 'Parent',
            'letter_macro_text' => 'Letter macro text',
        );
    }

    protected function afterValidate()
    {
        if ($this->disorder_id && $this->finding_id) {
            $this->addError('disorder_id', 'Cannot set both disorder and finding');
            $this->addError('finding_id', 'Cannot set both disorder and finding');
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->disorder) {
            return 'disorder';
        } elseif ($this->finding) {
            return 'finding';
        } elseif ($this->disorder_id || $this->finding_id) {
            // Finding or disorder is inactive
            return;
        } else {
            return 'none';
        }
    }

    /**
     * @return Disorder|Finding
     */
    public function getDisorderOrFinding()
    {
        if ($this->disorder) {
            return $this->disorder;
        } elseif ($this->finding) {
            return $this->finding;
        }
    }

    public function getConditionLabel()
    {
        // FIXME: Add label column (moved from COD alternative label)
        if (false /*$this->label*/) {
            return $this->label;
        } elseif ($this->getDisorderOrFinding()) {
            return $this->getDisorderOrFinding()->term;
        } else {
            return 'None';
        }
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('disorder_id', $this->disorder_id, true);
        $criteria->compare('finding_id', $this->finding_id, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
