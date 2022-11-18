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

use OE\factories\models\traits\HasFactory;

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
    use HasFactory;
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
    const SELECTION_LABEL_RELATION = 'disorder';
    const SELECTION_ORDER = 'subspecialty.name, t.display_order';
    const SELECTION_WITH = 'subspecialty';

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
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
            array('disorder_id, finding_id', 'containsDisorderOrFinding'),
            array('disorder_id, alternate_disorder_id', 'length', 'max' => 20),
            array('finding_id, group_id, subspecialty_id', 'length', 'max' => 10),
            array('alternate_disorder_label', 'RequiredIfFieldValidator', 'field' => 'alternate_disorder_id', 'value' => true),
            array('id, disorder_id, finding_id, group_id, alternate_disorder_id, subspecialty_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @param $object
     * @param $attribute
     * @return bool
     */
    public function containsDisorderOrFinding($object, $attribute)
    {
        if (empty($this->disorder_id) && empty($this->finding_id)) {
            $this->addError($object, Yii::t('user', 'At least one disorder or finding must be selected.'));
            return false;
        }

        return true;
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
            'institutions' => array(self::MANY_MANY, 'Institution', $this->tableName() . '_institution(' . $this->tableName() . '_id, institution_id)'),
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

        $criteria->compare('id', $this->id);
        $criteria->compare('disorder_id', $this->disorder_id);
        $criteria->compare('finding_id', $this->finding_id);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('alternate_disorder_id', $this->subspecialty_id);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, false, 'OR');

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
            return null;
        } else {
            return 'none';
        }
    }

    /**
     * @return Disorder|Finding
     * @throws CDbException
     */
    public function getDisorderOrFinding()
    {
        if ($this->disorder) {
            return $this->disorder;
        } elseif ($this->finding) {
            return $this->finding;
        }
        throw new CDbException('Cannot find disorder or finding for common ophthalmic disorder');
    }

    /**
     * Fetch options list of disorders (and optionally findings).
     *
     * @param Firm|null $firm
     * @param bool $include_findings
     * @param bool $include_patient_disorders
     * @param null $patient
     * @return array
     *
     * @throws CException
     */
    public static function getList($firm, $include_findings = false, $include_patient_disorders = false, $patient = null)
    {
        if (empty($firm)) {
            throw new CException('Firm is required.');
        }
        $disorders = array();
        $prefix = '';
        if ($include_findings) {
            $prefix = 'disorder-';
        }

        if ($include_patient_disorders && isset($patient)) {
            $patient_disorders = Disorder::model()->getPatientDisorders($patient->id);
            foreach ($patient_disorders as $disorder) {
                $disorders[$prefix . $disorder->id] = $disorder->term;
            }
        }

        if ($firm->serviceSubspecialtyAssignment) {
            $ss_id = $firm->getSubspecialtyID();
            $with = array('disorder');
            if ($include_findings) {
                $with = array(
                    'disorder' => array('joinType' => 'LEFT JOIN'),
                    'finding' => array('joinType' => 'LEFT JOIN'),
                );
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

        return array_unique($disorders);
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
            $criteria = new CDbCriteria();
            $criteria->join = "JOIN common_ophthalmic_disorder_institution codi ON t.id = codi.common_ophthalmic_disorder_id";
            $criteria->compare('t.subspecialty_id', $ss_id);
            $criteria->compare('codi.institution_id', $firm->institution_id);

            $cods = self::model()->with(array(
                'finding' => array('joinType' => 'LEFT JOIN'),
                'disorder' => array('joinType' => 'LEFT JOIN'),
                'group',
            ))->findAll($criteria);

            foreach ($cods as $cod) {
                if ($cod->type) {
                    $disorder = array();
                    $group = ($cod->group) ? $cod->group->name : '';
                    $disorder['type'] = $cod->type;
                    $disorder['id'] = ($cod->disorderOrFinding) ? $cod->disorderOrFinding->id : null;
                    $disorder['label'] = ($cod->disorderOrFinding) ? $cod->disorderOrFinding->term : 'None';
                    $disorder['is_glaucoma'] = isset($cod->disorder->term)? (strpos(strtolower($cod->disorder->term), 'glaucoma')) !== false : false;
                    $disorder['group'] = $group;
                    $disorder['group_id'] = isset($cod->group) ? $cod->group->id : null;
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

    /**
     * Fetch disorders that are in a group
     *
     * @return array
     */
    public static function getDisordersInGroup()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('group_id IS NOT NULL');
        $disorders_in_group = new CActiveDataProvider('CommonOphthalmicDisorder', array(
            'criteria' => $criteria,
            'pagination' => false,
        ));
        return array_values(
            array_unique(
                array_map(function ($disorder) {
                        return $disorder->group_id;
                },
                    $disorders_in_group->getData()
                )
            )
        );
    }
}
