<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "common_ophthalmic_disorder".
 *
 * The followings are the available columns in table 'common_ophthalmic_disorder':
 * @property integer $id
 * @property integer $disorder_id
 * @property integer $finding_id
 * @property integer $subspecialty_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 * @property Finding $finding
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
	 * @return CommonOphthalmicDisorder the static model class
	 */
	public static function model($className=__CLASS__)
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
		return array('order' => $this->getTableAlias(true, false) . '.display_order');
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
			array('disorder_id, finding_id, alternate_disorder_id, subspecialty_id', 'length', 'max'=>10),
			array('alternate_disorder_label','RequiredIfFieldValidator','field' => 'alternate_disorder_id', 'value' => true),
			array('id, disorder_id, finding_id, alternate_disorder_id, subspecialty_id', 'safe', 'on'=>'search'),
		);
	}

	protected function afterValidate()
	{
		if($this->disorder_id && $this->finding_id) {
			$this->addError('disorder_id','Cannot set both disorder and finding');
			$this->addError('finding_id','Cannot set both disorder and finding');
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
			'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id', 'condition' => 'disorder.active = 1'),
			'finding' => array(self::BELONGS_TO, 'Finding', 'finding_id', 'condition' => 'finding.active = 1'),
			'alternate_disorder' => array(self::BELONGS_TO, 'Disorder', 'alternate_disorder_id', 'condition' => 'alternate_disorder.active = 1'),
			'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
			'secondary_to' => array(self::HAS_MANY, 'SecondaryToCommonOphthalmicDisorder', 'parent_id'),
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
			'alternate_disorder_id' => 'Alternate Disorder'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('disorder_id',$this->disorder_id,true);
		$criteria->compare('finding_id',$this->finding_id,true);
		$criteria->compare('alternate_disorder_id',$this->subspecialty_id,true);
		$criteria->compare('subspecialty_id',$this->subspecialty_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		if($this->disorder) {
			return 'disorder';
		} else if($this->finding) {
			return 'finding';
		} else {
			return 'none';
		}
	}

	/**
	 * @return Disorder|Finding
	 */
	public function getDisorderOrFinding() {
		if($this->disorder) {
			return $this->disorder;
		} else if($this->finding) {
			return $this->finding;
		}
	}

	/**
	 * Fetch options list of disorders (and optionally findings)
	 * @param Firm $firm
	 * @param bool $include_findings
	 * @return array
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
			$join = 'JOIN disorder ON disorder.id = t.disorder_id AND disorder.active = 1';
			$prefix = '';
			if($include_findings) {
				$join = 'LEFT '.$join.' LEFT JOIN finding ON finding.id = t.finding_id AND finding.active = 1';
				$prefix = 'disorder-';
			}
			$cods = self::model()->findAll(array(
				'condition' => 't.subspecialty_id = :subspecialty_id',
				'join' => $join,
				'params' => array(':subspecialty_id' => $ss_id),
			));
			foreach($cods as $cod) {
				if($cod->finding) {
					$disorders['finding-'.$cod->finding->id] = $cod->finding->name;
				} else if($cod->disorder) {
					$disorders[$prefix.$cod->disorder->id] = $cod->disorder->term;
				}
			}
		}
		return $disorders;
	}

	/**
	 * Fetch array of disorders and associated secondary to disorders (and optionally findings)
	 * @param Firm $firm
	 * @return array
	 * @throws CException
	 */
	public static function getListWithSecondaryTo(Firm $firm)
	{
		if (empty($firm)) {
			throw new CException('Firm is required');
		}
		$disorders = array();
		if ($ss_id = $firm->getSubspecialtyID()) {
			$join = 'LEFT JOIN disorder ON disorder.id = t.disorder_id AND disorder.active = 1';
			$join .= ' LEFT JOIN finding ON finding.id = t.finding_id AND finding.active = 1';
			$cods = self::model()->findAll(array(
				'condition' => 't.subspecialty_id = :subspecialty_id',
				'join' => $join,
				'params' => array(':subspecialty_id' => $ss_id),
			));
			foreach ($cods as $cod) {
				$disorder = array();
				$disorder['type'] = $cod->type;
				$disorder['id'] = $cod->disorderOrFinding ? $cod->disorderOrFinding->id : null;
				$disorder['label'] = $cod->disorderOrFinding ? $cod->disorderOrFinding->term : 'None';
				$disorder['alternate_id'] = 42; // FIXME: Implement
				$disorder['secondary'] = $cod->getSecondaryToList();
				$disorders[] = $disorder;
			}
		}
		return $disorders;
	}

	/**
	 * Fetch array of secondary disorders/findings
	 * @return array
	 */
	public function getSecondaryToList()
	{
		$secondaries = array();
		foreach($this->secondary_to as $secondary_to) {
			$secondary = array();
			$secondary['type'] = $secondary_to->type;
			$secondary['id'] = $secondary_to->disorderOrFinding ? $secondary_to->disorderOrFinding->id : null;
			$secondary['label'] = $secondary_to->conditionLabel;
			$secondaries[] = $secondary;
		}
		return $secondaries;
	}

	public function getSelectionLabel()
	{
		$lbl = $this->subspecialty->name . " - ";
		$lbl .= $this->disorderOrFinding ? $this->disorderOrFinding->term : 'None';
		return $lbl;
	}
}
