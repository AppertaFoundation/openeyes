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
 * This is the model class for table "referral".
 *
 * The followings are the available columns in table 'referral':
 * @property integer $id
 * @property string $refno
 * @property integer $patient_id
 * @property integer $referral_type_id
 * @property date $received_date
 * @property date $closed_date
 * @property string $referrer
 * @property integer $firm_id
 * @property integer $service_subspecialty_assignment_id // MW: this is here because sometimes the referrer is a pas_code which doesn't map to a firm with the correct subspecialty
 *
 * @property RTT[] $rtts
 * @property Firm $firm
 * @property ServiceSubspecialtyAssignment $serviceSubpsecialtyAssignment
 * @property ReferralType $reftype
 */
class Referral extends BaseActiveRecordVersioned
{

	public $use_pas = TRUE;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Referral the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Suppress PAS integration
	 * @return Referral
	 */
	public function noPas()
	{
		// Clone to avoid singleton problems with use_pas flag
		$model = clone $this;
		$model->use_pas = FALSE;
		return $model;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'referral';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
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
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'serviceSubspecialtyAssignment' => array(self::BELONGS_TO, 'ServiceSubspecialtyAssignment', 'service_subspecialty_assignment_id'),
			'gp' => array(self::BELONGS_TO, 'Gp', 'gp_id'),
			'reftype' => array(self::BELONGS_TO, 'ReferralType', 'referral_type_id'),
			'rtts' => array(self::HAS_MANY, 'RTT', 'referral_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
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

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Pass through use_pas flag to allow pas supression
	 * @see CActiveRecord::instantiate()
	 */
	protected function instantiate($attributes)
	{
		$model = parent::instantiate($attributes);
		$model->use_pas = $this->use_pas;
		return $model;
	}

	/**
	 * Returns string description of the referral
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$desc = array();
		$desc[] = $this->NHSDate('received_date');

		if ($this->firm) {
			$desc[] = $this->firm->getNameAndSubspecialty();
		}
		elseif ($ssa = $this->serviceSubspecialtyAssignment) {
			$desc[] = $ssa->subspecialty->name;
		}
		$desc[] = $this->reftype->getDescription();
		$desc[] = "(" . $this->refno . ")";
		if ($this->closed_date) {
			$desc[] = "(closed - " . $this->NHSDate('closed_date') . ")";
		}

		return implode(' ', $desc);

	}

	/**
	 * Get the active RTTs attached to this referral
	 *
	 * @return RTT[]
	 */
	public function getActiveRTT()
	{
		$res = array();
		foreach ($this->rtts as $rtt) {
			if ($rtt->active) {
				$res[] = $rtt;
			}
		}
		return $res;
	}
}
