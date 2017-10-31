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
 * This is the model class for table "common_ophthalmic_disorder".
 *
 * The followings are the available columns in table 'common_ophthalmic_disorder':
 *
 * @property int $id
 * @property int $disorder_id
 * @property int $finding_id
 * @property int $group_id
 * @property int $subspecialty_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 * @property Finding $finding
 * @property Group $group
 * @property Subspecialty $subspecialty
 * @property SecondaryToCommonOphthalmicDisorder[] $secondary_to
 * @property Disorder[] $secondary_to_disorders
 */
class CommonOphthalmicDisorder extends BaseActiveRecordVersioned
{
    const SELECTION_LABEL_FIELD = 'disorder_id';
    const SELECTION_LABEL_RELATION = 'disorder';
    const SELECTION_ORDER = 'subspecialty.name, t.display_order';
    const SELECTION_WITH = 'subspecialty';

    /**
     * Returns the static model of the specified AR class.
     *
     * @return CommonOphthalmicDisorder the static model class
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
        return 'common_ophthalmic_disorder';
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
            array('subspecialty_id', 'required'),
            array('disorder_id, finding_id, group_id, alternate_disorder_id, subspecialty_id', 'length', 'max' => 10),
            array('alternate_disorder_label', 'RequiredIfFieldValidator', 'field' => 'alternate_disorder_id', 'value' => true),
            array('id, disorder_id, finding_id, group_id, alternate_disorder_id, subspecialty_id', 'safe', 'on' => 'search'),
        );
    }

    protected function afterValidate()
    {
        if ($this->disorder_id && $this->finding_id) {
            $this->addError('disorder_id', 'Cannot set both disorder and finding');
            $this->addError('finding_id', 'Cannot set both disorder and finding');
        }
        if ($this->subspecialty_id && !$this->disorder_id && !$this->finding_id) {
            // check this is the only COD for the subspecialty that has no disorder or finding
            $criteria = new CDbCriteria();
            $criteria->addCondition('subspecialty_id = :sid');
            $criteria->addColumnCondition(array('disorder_id' => null, 'finding_id' => null));
            $params = array(':sid' => $this->subspecialty_id);
            if ($this->id) {
                $criteria->addCondition('id != :id');
                $params[':id'] = $this->id;
            }
            $criteria->params = $params;
            // run query and raise validation error if any are found
            if (self::count($criteria)) {
                $this->addError('subspecialty_id', 'Cannot have more than one null entry for the subspecialty');
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id', 'on' => 'disorder.active = 1'),
            'finding' => array(self::BELONGS_TO, 'Finding', 'finding_id', 'on' => 'finding.active = 1'),
            'alternate_disorder' => array(self::BELONGS_TO, 'Disorder', 'alternate_disorder_id', 'on' => 'alternate_disorder.active = 1'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'secondary_to' => array(self::HAS_MANY, 'SecondaryToCommonOphthalmicDisorder', 'parent_id'),
            'group' => array(self::BELONGS_TO, 'CommonOphthalmicDisorderGroup', 'group_id'),
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
            'subspecialty_id' => 'Subspecialty',
            'group_id' => 'Group',
            'alternate_disorder_id' => 'Alternate Disorder',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('disorder_id', $this->disorder_id, true);
        $criteria->compare('finding_id', $this->finding_id, true);
        $criteria->compare('group_id', $this->group_id, true);
        $criteria->compare('alternate_disorder_id', $this->subspecialty_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
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

    /**
     * Fetch options list of disorders (and optionally findings).
     *
     * @param Firm $firm
     * @param bool $include_findings
     *
     * @return array
     *
     * @throws CException
     */
    public static function getList(Firm $firm, $include_findings = false)
    {
        if (empty($firm)) {
            throw new CException('Firm is required.');
        }
        $disorders = array();
        if ($firm->serviceSubspecialtyAssignment) {
            $ss_id = $firm->getSubspecialtyID();
            $with = array('disorder');
            $prefix = '';
            if ($include_findings) {
                $with = array(
                    'disorder' => array('joinType' => 'LEFT JOIN'),
                    'finding' => array('joinType' => 'LEFT JOIN'),
                );
                $prefix = 'disorder-';
            }
            $cods = self::model()->with($with)->findAll(array(
                'condition' => 't.subspecialty_id = :subspecialty_id',
                'params' => array(':subspecialty_id' => $ss_id),
            ));
            foreach ($cods as $cod) {
                if ($cod->finding && $include_findings) {
                    $disorders['finding-'.$cod->finding->id] = $cod->finding->name;
                } elseif ($cod->disorder) {
                    $disorders[$prefix.$cod->disorder->id] = $cod->disorder->term;
                }
            }
        }

        return $disorders;
    }

    /**
     * Fetch array of disorders and associated secondary to disorders (and optionally findings).
     *
     * @param Firm $firm
     *
     * @return array
     *
     * @throws CException
     */
    public static function getListByGroupWithSecondaryTo(Firm $firm)
    {
        if (empty($firm)) {
            throw new CException('Firm is required');
        }
        $disorders = array();
        if ($ss_id = $firm->getSubspecialtyID()) {
            $cods = self::model()->with(array(
                'finding' => array('joinType' => 'LEFT JOIN'),
                'disorder' => array('joinType' => 'LEFT JOIN'),
                'group',
            ))->findAll(array(
                'condition' => 't.subspecialty_id = :subspecialty_id',
                'params' => array(':subspecialty_id' => $ss_id),
            ));
            foreach ($cods as $cod) {
                if ($cod->type) {
                    $disorder = array();
                    $group = ($cod->group) ? $cod->group->name : '';
                    $disorder['type'] = $cod->type;
                    $disorder['id'] = ($cod->disorderOrFinding) ? $cod->disorderOrFinding->id : null;
                    $disorder['label'] = ($cod->disorderOrFinding) ? $cod->disorderOrFinding->term : 'None';
                    $disorder['group'] = $group;
                    $disorder['alternate'] = $cod->alternate_disorder_id ?
                        array(
                            'id' => $cod->alternate_disorder_id,
                            'label' => $cod->alternate_disorder->term,
                            'selection_label' => $cod->alternate_disorder_label,
                            // only allow disorder alternates at this point so type is hard code
                            'type' => 'disorder',
                        ) : null;
                    $disorder['secondary'] = $cod->getSecondaryToList();
                    $disorders[] = $disorder;
                }
            }
        }

        return $disorders;
    }

    /**
     * Fetch array of secondary disorders/findings.
     *
     * @return array
     */
    public function getSecondaryToList()
    {
        $secondaries = array();
        foreach ($this->secondary_to as $secondary_to) {
            if ($secondary_to->type) {
                $secondary = array();
                $secondary['type'] = $secondary_to->type;
                $secondary['id'] = $secondary_to->disorderOrFinding ? $secondary_to->disorderOrFinding->id : null;
                $secondary['label'] = $secondary_to->conditionLabel;
                $secondaries[] = $secondary;
            }
        }

        return $secondaries;
    }

    /**
     * Label for use in dropdowns.
     *
     * @return string
     */
    public function getSelectionLabel()
    {
        return $this->subspecialty->name.' - '.($this->disorderOrFinding ? $this->disorderOrFinding->term : 'None');
    }
}
